<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\Core\Services\EmailService;
use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private readonly Nette\Database\Explorer $database,
        private readonly EmailService $emailService,
    ) {
        parent::__construct();
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Auth:login');
        }
    }

    public function renderDefault(): void
    {
        // Get total registered users
        $totalUsers = $this->database->table('users')->count();

        // Get total administrators
        $totalAdmins = $this->database->table('users')
            ->where('role_id', 1)
            ->count();

        // Get the 3 most recent users
        $recentUsers = $this->database->table('users')
            ->order('created_at DESC')
            ->limit(3)
            ->fetchAll();

        $this->template->totalUsers = $totalUsers;
        $this->template->totalAdmins = $totalAdmins;
        $this->template->recentUsers = $recentUsers;
    }

}
