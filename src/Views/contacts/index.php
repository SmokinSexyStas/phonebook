<?php
$title = 'Мої контакти';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Мої контакти</h1>
            <p class="mt-2 text-sm text-gray-700">
                Всього контактів: <span id="total-contacts"><?= $pagination['total'] ?></span>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <button
                type="button"
                id="add-contact-btn"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Додати контакт
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div id="add-contact-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Додати новий контакт</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="add-contact-form" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">Ім'я *</label>
                            <input
                                type="text"
                                name="first_name"
                                id="first_name"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Введіть ім'я"
                            >
                            <div class="text-red-600 text-sm mt-1 hidden" id="first_name_error"></div>
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Прізвище *</label>
                            <input
                                type="text"
                                name="last_name"
                                id="last_name"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Введіть прізвище"
                            >
                            <div class="text-red-600 text-sm mt-1 hidden" id="last_name_error"></div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Телефон *</label>
                            <input
                                type="tel"
                                name="phone"
                                id="phone"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="+380XXXXXXXXX"
                            >
                            <div class="text-red-600 text-sm mt-1 hidden" id="phone_error"></div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="example@email.com"
                            >
                            <div class="text-red-600 text-sm mt-1 hidden" id="email_error"></div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-medium text-gray-700">Фото</label>
                            <input id="image" name="image" type="file" accept="image/jpeg,image/png" class="mt-2 text-sm">
                            <p class="text-xs text-gray-500">PNG, JPG до 5MB</p>
                            <div class="text-red-600 text-sm mt-1 hidden" id="image_error"></div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            id="cancel-btn"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Скасувати
                        </button>
                        <button
                            type="submit"
                            id="submit-btn"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <span class="submit-text">Додати контакт</span>
                            <svg class="submit-loader hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="contacts-container">
        <?php require_once __DIR__ . "/../components/contacts_list.php" ?>
    </div>
</div>

<script src="/assets/js/contacts.js"></script>
