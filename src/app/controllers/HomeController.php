<?php
class HomeController extends Controller
{
    public function index(): void {
        if ($this->isAuthenticated()) {
            $this->redirect('/u/' . $this->currentUsername());
        }

        $this->redirect('/login');
    }
}
