<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'unique' => 'The :attribute has already been taken.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'confirmed' => 'The :attribute confirmation does not match.',
    'in' => 'The selected :attribute is invalid.',
    'string' => 'The :attribute must be a string.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'user_type' => 'user type',
    ],
    'auth' => [
        'failed' => 'These credentials do not match our records.',
    ],
    'contracterror' => 'You are not a platform owner, so you cannot access this page.',
];