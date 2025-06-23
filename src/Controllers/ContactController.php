<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Middleware\AuthMiddleware;
use App\Utils\SessionUtil;
use App\Utils\Validator;
use App\Utils\FileUpload;
use JetBrains\PhpStorm\NoReturn;

class ContactController extends BaseController
{
    private Contact $contactModel;
    private FileUpload $fileUpload;

    public function __construct()
    {
        parent::__construct();
        $this->contactModel = new Contact();
        $this->fileUpload = new FileUpload();

        AuthMiddleware::requireAuth();
    }

    public function index(): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $perPage = $config['pagination']['per_page'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $userId = $this->getCurrentUser()['id'];

        $pagination = $this->contactModel->getPaginationData($userId, $page, $perPage);
        if ($page > $pagination['total_pages'] && $pagination['total_pages'] > 0) {
            $this->redirect('/contacts');
        }
        $contacts = $this->contactModel->findAllByUser($userId, $page, $perPage);


        $this->view('contacts/index', [
            'contacts' => $contacts,
            'pagination' => $pagination,
            'fileUpload' => $this->fileUpload
        ]);
    }

    #[NoReturn] public function list(): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $perPage = $config['pagination']['per_page'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $userId = $this->getCurrentUser()['id'];

        $contacts = $this->contactModel->findAllByUser($userId, $page, $perPage);
        $pagination = $this->contactModel->getPaginationData($userId, $page, $perPage);

        ob_start();
        extract([
            'contacts' => $contacts,
            'pagination' => $pagination,
            'fileUpload' => $this->fileUpload
        ]);
        require __DIR__ . '/../Views/components/contacts_list.php';
        $html = ob_get_clean();

        $this->json([
            'success' => true,
            'html' => $html,
            'pagination' => $pagination
        ]);
    }

    public function show(int $id): void
    {
        $userId = $this->getCurrentUser()['id'];
        $contact = $this->contactModel->findById($id, $userId);

        if (!$contact) {
            SessionUtil::flash('error', 'Контакт не знайдено');
            $this->redirect('/contacts');
        }

        $this->view('contacts/view', [
            'contact' => $contact,
            'fileUpload' => $this->fileUpload
        ]);
    }

    #[NoReturn] public function create(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Недійсний CSRF токен'], 403);
        }

        $validator = new Validator([
            'first_name' => $this->getPost('first_name'),
            'last_name' => $this->getPost('last_name'),
            'phone' => $this->getPost('phone'),
            'email' => $this->getPost('email'),
        ], [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'phone' => 'required|phone',
            'email' => 'required|email'
        ]);

        if (!$validator->validate()) {
            $this->json([
                'success' => false,
                'errors' => $validator->getErrors()
            ], 422);
        }

        $userId = $this->getCurrentUser()['id'];
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = $this->fileUpload->upload($_FILES['image'], $userId);

            if (!$imagePath) {
                $errors = ['image' => $this->fileUpload->getErrors()];
                $this->json(['success' => false, 'errors' => $errors], 422);
            }
        }

        $contactData = [
            'user_id' => $userId,
            'first_name' => trim($this->getPost('first_name')),
            'last_name' => trim($this->getPost('last_name')),
            'phone' => trim($this->getPost('phone')),
            'email' => trim($this->getPost('email')),
            'image_path' => $imagePath
        ];

        $contactId = $this->contactModel->create($contactData);

        if ($contactId) {
            $contact = $this->contactModel->findById($contactId, $userId);
            $this->json([
                'success' => true,
                'message' => 'Контакт успішно додано',
                'contact' => $contact,
                'image_url' => $imagePath ? $this->fileUpload->getUrl($imagePath) : null
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Помилка створення контакту'], 500);
        }
    }

    #[NoReturn] public function delete(int $id): void
    {
        if (!$this->validateCsrf()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Недійсний CSRF токен'], 403);
            }
            SessionUtil::flash('error', 'Недійсний запит');
            $this->redirect('/contacts');
        }

        $userId = $this->getCurrentUser()['id'];
        $contact = $this->contactModel->findById($id, $userId);

        if (!$contact) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Контакт не знайдено'], 404);
            }
            SessionUtil::flash('error', 'Контакт не знайдено');
            $this->redirect('/contacts');
        }

        if ($contact['image_path']) {
            $this->fileUpload->delete($contact['image_path']);
        }

        if ($this->contactModel->delete($id, $userId)) {
            if ($this->isAjax()) {
                $config = require __DIR__ . '/../../config/config.php';
                $perPage = $config['pagination']['per_page'];
                $page = max(1, (int)($_POST['page'] ?? 1));
                error_log(print_r($_POST, true));

                $pagination = $this->contactModel->getPaginationData($userId, $page, $perPage);
                $shouldRedirectToPrevPage = false;

                if ($page > $pagination['total_pages'] && $pagination['total_pages'] > 0) {
                    $shouldRedirectToPrevPage = true;
                }

                $this->json([
                    'success' => true,
                    'message' => 'Контакт успішно видалено',
                    'deleted_id' => $id,
                    'should_redirect_to_prev_page' => $shouldRedirectToPrevPage
                ]);
            }
            SessionUtil::flash('success', 'Контакт видалено');
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Помилка видалення контакту'], 500);
            }
            SessionUtil::flash('error', 'Помилка видалення контакту');
        }
        $this->redirect('/contacts');
    }
}