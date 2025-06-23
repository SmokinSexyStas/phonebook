<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Телефонна книга' ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<meta name="csrf-token" content="<?= $csrf_token ?>">
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold text-gray-900">
                    <a href="/" class="hover:text-blue-600">📞 Телефонна книга</a>
                </h1>
            </div>
            <?php if ($user): ?>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Привіт, <?= htmlspecialchars($user['login']) ?>!</span>
                    <a href="/contacts" class="text-blue-600 hover:text-blue-800">Контакти</a>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                        <button type="submit" class="text-red-600 hover:text-red-800">Вийти</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" id="error-alert">
        <div class="flex justify-between items-center">
            <div>
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <button onclick="document.getElementById('error-alert').remove()" class="text-red-700 hover:text-red-900">
                <span class="sr-only">Закрити</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" id="success-alert">
        <div class="flex justify-between items-center">
            <p><?= htmlspecialchars($success) ?></p>
            <button onclick="document.getElementById('success-alert').remove()" class="text-green-700 hover:text-green-900">
                <span class="sr-only">Закрити</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?= $content ?>
</main>

</body>
</html>