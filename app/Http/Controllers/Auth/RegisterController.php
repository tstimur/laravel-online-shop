<?php

namespace App\Http\Controllers\Auth;

use App\DTO\RegisterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Service\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    )
    {}

    public function showForm(): Factory|View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): Response
    {
        $dto = RegisterDto::fromRequest($request);
        $user = $this->userService->register($dto);

        return response(['success', "Welcome,{$user->full_name}!"], Response::HTTP_CREATED);
    }
}
