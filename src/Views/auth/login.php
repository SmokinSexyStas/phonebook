<div class="min-h-full flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                📞 Телефонна книга
            </h2>
            <?php if (!$user): ?>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Увійдіть до свого акаунту
                </p>
            <?php endif; ?>
        </div>

        <?php if (!$user): ?>
            <form class="mt-8 space-y-6" method="POST" action="/login">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">

                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="login" class="sr-only">Логін</label>
                        <input
                            id="login"
                            name="login"
                            type="text"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?= (isset($errors['login']) || isset($errors['password'])) ? 'border-red-500' : '' ?>"
                            placeholder="Логін"
                            value="<?= htmlspecialchars($form_data['login'] ?? '') ?>"
                        >
                    </div>
                    <div>
                        <label for="password" class="sr-only">Пароль</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm <?= (isset($errors['login']) || isset($errors['password'])) ? 'border-red-500' : '' ?>"
                            placeholder="Пароль"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Запам'ятати мене
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="/register" class="font-medium text-blue-600 hover:text-blue-500">
                            Немає акаунту? Зареєструватися
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Увійти
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="flex justify-center mt-6">
                <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="window.location.href='/contacts'"
                >
                    Мої контакти
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
