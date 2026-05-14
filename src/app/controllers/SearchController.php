<?php
class SearchController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $results = $query !== '' ? $this->users->searchWithPhotoCounts($query) : [];
        $shell = $this->isAuthenticated()
            ? $this->buildMemberShell('Buscar')
            : $this->buildGuestShell('Buscar');

        $this->render('search/index', $shell + [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
