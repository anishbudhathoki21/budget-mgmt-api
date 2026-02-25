<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\RegisterInput;
use App\Dto\RegisterResponse;
use App\State\RegisterProcessor;

#[ApiResource(
    operations: [
      new Post(
        uriTemplate: '/register',
        input: RegisterInput::class,
        output: RegisterResponse::class,
        processor: RegisterProcessor::class,
        status: 201
      )
    ]
)]

class Register
{

}