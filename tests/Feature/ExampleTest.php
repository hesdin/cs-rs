<?php

use Inertia\Testing\AssertableInertia;

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Landing')
            ->where('hospitalName', 'Rumah Sakit Ibnu Sina YW-UMI Makassar')
            ->has('contact')
            ->has('testimonials', 3)
            ->has('insights', 3));
});
