<?php

declare(strict_types=1);

namespace App\UI\Home;

use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(private Nette\Database\Explorer $database) {
        parent::__construct();
    }


    public function renderDefault(): void
    {
        $this->template->posts = $this->database
            ->table('posts')
            ->order('created_at DESC')
            ->limit(5);
    }

}
