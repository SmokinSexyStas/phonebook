$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const modal = $('#add-contact-modal');
    const addContactForm = $('#add-contact-form');
    const closeModalButton = $('#close-modal');
    const cancelButton = $('#cancel-btn');
    const submitButton = $('#submit-btn');

    function closeModal() {
        modal.addClass('hidden');
        $('body').css('overflow', 'auto');
        addContactForm[0].reset();
        clearErrors();
    }

    function clearErrors() {
        $('[id$="_error"]').each(function() {
            $(this).addClass('hidden').text('');
        });
        addContactForm.find('input, select, textarea').removeClass('border-red-500');
    }

    function showErrors(errors) {
        clearErrors();
        $.each(errors, function(field, messages) {
            const errorElement = $('#' + field + '_error');
            const inputElement = $('#' + field + ', [name="' + field + '"]');
            if (errorElement.length && messages.length > 0) {
                errorElement.text(messages[0]).removeClass('hidden');
            }
            if (inputElement.length) {
                inputElement.addClass('border-red-500');
            }
        });
    }

    function loadContactsPage(page = 1) {
        $.ajax({
            url: `/contacts/list?page=${page}`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                $('#contacts-container').html(data.html);

                const newUrl = `/contacts?page=${page}`;
                window.history.pushState({page: page}, '', newUrl);
            },
            error: function(xhr) {
                console.error('Помилка завантаження контактів:', xhr);
            }
        });
    }

    $(document).on('click', '[id^="add-contact-btn"]', function() {
        modal.removeClass('hidden');
        $('body').css('overflow', 'hidden');
    });

    closeModalButton.on('click', closeModal);
    cancelButton.on('click', closeModal);

    modal.on('click', function(e) {
        if (e.target === modal[0]) {
            closeModal();
        }
    });

    addContactForm.on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitText = submitButton.find('.submit-text');
        const submitLoader = submitButton.find('.submit-loader');

        submitButton.prop('disabled', true);
        submitText.text('Додавання...');
        submitLoader.removeClass('hidden');

        $.ajax({
            url: '/contacts/create',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data.success) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentPage = urlParams.get('page') || 1;

                    incrementTotalContacts();

                    closeModal();
                    loadContactsPage(currentPage);
                } else {
                    if (data.errors) {
                        showErrors(data.errors);
                    } else {
                        console.error(data.message || 'Виникла помилка при додаванні контакту');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                        showErrors(response.errors);
                    } else {
                        console.error(response.message || 'Виникла помилка при додаванні контакту');
                    }
                } catch (e) {
                    console.error('Виникла помилка при додаванні контакту');
                }
            },
            complete: function() {
                submitButton.prop('disabled', false);
                submitText.text('Додати контакт');
                submitLoader.addClass('hidden');
            }
        });
    });

    $(document).on('click', '.delete-contact-btn', function(e) {
        e.preventDefault();

        if (!confirm('Ви впевнені, що хочете видалити цей контакт?')) {
            return;
        }

        const contactId = $(this).data('contact-id');
        const deleteButton = $(this);
        const originalText = deleteButton.find('span').text();

        deleteButton.prop('disabled', true);
        deleteButton.find('span').text('Видалення...');

        const urlParams = new URLSearchParams(window.location.search);
        let currentPage = parseInt(urlParams.get('page')) || 1;

        $.ajax({
            url: `/contacts/${contactId}/delete`,
            method: 'POST',
            data: {
                _token: csrfToken,
                page: currentPage,
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data.success) {
                    if (data.should_redirect_to_prev_page) {
                        currentPage = Math.max(1, currentPage - 1);
                        const newUrl = currentPage === 1 ? '/contacts' : `/contacts?page=${currentPage}`;
                        window.history.pushState({page: currentPage}, '', newUrl);
                    }

                    decrementTotalContacts();
                    loadContactsPage(currentPage);
                } else {
                    console.error(data.message || 'Виникла помилка при видаленні контакту');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error(response.message || 'Виникла помилка при видаленні контакту');
                } catch (e) {
                    console.error('Виникла помилка при видаленні контакту');
                }
            },
            complete: function() {
                deleteButton.prop('disabled', false);
                deleteButton.find('span').text(originalText);
            }
            }
        });
    });

    function incrementTotalContacts() {
        const totalContactsElement = document.getElementById('total-contacts');
        let currentValue = parseInt(totalContactsElement.innerText, 10);
        totalContactsElement.innerText = currentValue + 1;
    }

    function decrementTotalContacts() {
        const totalContactsElement = document.getElementById('total-contacts');
        let currentValue = parseInt(totalContactsElement.innerText, 10);
        if (currentValue > 0) {
            totalContactsElement.innerText = currentValue - 1;
        }
    }

});