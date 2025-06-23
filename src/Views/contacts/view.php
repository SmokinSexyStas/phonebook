<?php
$title = 'Перегляд контакту';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">
                <?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?>
            </h1>
            <p class="mt-2 text-sm text-gray-700">
                Деталі контакту
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="/contacts"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:w-auto">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Назад до контактів
            </a>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex items-center space-x-6">
            <?php if ($contact['image_path']): ?>
                <img class="w-16 h-16 rounded-full object-cover"
                     src="<?= htmlspecialchars($fileUpload->getUrl($contact['image_path'])) ?>"
                     alt="<?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?>">
            <?php else: ?>
                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                    <span class="text-lg font-medium text-gray-700">
                        <?= strtoupper(substr($contact['first_name'], 0, 1) . substr($contact['last_name'], 0, 1)) ?>
                    </span>
                </div>
            <?php endif; ?>
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Контакт створено: <?= date('d.m.Y H:i', strtotime($contact['created_at'])) ?>
                </p>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Телефон</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= htmlspecialchars($contact['phone']) ?>
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <?= htmlspecialchars($contact['email']) ?>
                    </dd>
                </div>
            </dl>
        </div>
        <div class="px-4 py-5 sm:px-6">
            <form id="delete-contact-form" method="POST" action="/contacts/<?= $contact['id'] ?>/delete">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <button
                    type="submit"
                    id="delete-contact-btn"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Видалити контакт
                </button>
            </form>
        </div>
    </div>
</div>