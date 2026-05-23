<?php

test('registration screen redirects to get-started', function () {
    $response = $this->get('/register');

    $response->assertRedirect(route('get-started'));
});
