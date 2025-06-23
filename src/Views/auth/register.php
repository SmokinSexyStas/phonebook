<div class="min-h-full flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                📞 Телефонна книга
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Створіть новий акаунт
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="/register">
            <input type="hidden" name="_token" value="<?= $csrf_token ?>">

            <div class="space-y-4">
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700">Логін</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        required
                        maxlength="16"
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= isset($errors['login']) ? 'border-red-500' : '' ?>"
                        value="<?= htmlspecialchars($form_data['login'] ?? '') ?>"
                    >
                    <p class="mt-1 text-xs text-gray-500">До 16 символів, тільки латинські літери та цифри</p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Електронна пошта</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= isset($errors['email']) ? 'border-red-500' : '' ?>"
                        value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= isset($errors['password']) ? 'border-red-500' : '' ?>"
                    >
                    <p class="mt-1 text-xs text-gray-500">Не менше 6 символів, щонайменше одна цифра, одна велика та одна маленька літера</p>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700">Повторіть пароль</label>
                    <input
                            id="password_confirm"
                            name="password_confirm"
                            type="password"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= isset($errors['password_confirm']) ? 'border-red-500' : '' ?>"
                    >
                </div>
            </div>

            <div class="flex justify-end">
                <div class="text-sm">
                    <a href="/" class="font-medium text-blue-600 hover:text-blue-500">
                        Вже є акаунту? Увійти
                    </a>
                </div>
            </div>

            <div>
                <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Зареєструватися
                </button>
            </div>
        </form>