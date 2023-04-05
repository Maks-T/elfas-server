<?php

declare(strict_types=1);

namespace Elfas\Controllers;

/*use App\DB\Repository\UserRepository;
use App\Services\ServiceJWT;*/

use Elfas\Services\RequestService;


abstract class Controller
{
  /* protected UserRepository $userRepository;

  protected ServiceJWT $serviceJWT;*/

  protected RequestService $request;

  public function __construct()
  {
    /* $this->userRepository = new UserRepository();
    $this->serviceJWT = new ServiceJWT();*/
    $this->request = new RequestService();
  }
}
