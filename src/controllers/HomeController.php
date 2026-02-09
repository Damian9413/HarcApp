<?php

class HomeController
{
    public function index(): void
    {
        echo '<h1>HarcApp</h1>';
        echo '<p>Strona główna – tu będzie np. przekierowanie do logowania.</p>';
        echo '<p><a href="/">Odśwież</a></p>';
    }
}