<?php

namespace App\Service\Security;

use ErrorException;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityService
{

    /**
     * Permet de vérifier si l'user à le droit admin
     *
     * @param User $user
     * @param ?string $method
     * @return JsonResponse|null
     */
    public function ressourceRightsAdmin(User $user): ?JsonResponse
    {
        try {
            if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return new JsonResponse([
                    'exception' => "contact the administrator, your rights are not sufficient",
                    'errors' => ['user' => "admin only"],
                    "code" => Response::HTTP_BAD_REQUEST
                ]);
            }
            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception' => $e->getMessage(),
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
    }

    /**
     *  Si l'utilisateur est un prospect alors il n'a pas accès à la ressource
     *
     * @param User $user
     * @return JsonResponse|null
     */
    public function forbiddenProspect(User $user): ?JsonResponse
    {
        try {
            if (in_array(User::ROLE_PROSPECT, $user->getRoles())) {
                return new JsonResponse([
                    'exception' => "contact the administrator, your rights are not sufficient",
                    'errors' => ['user' => "forbiddenProspect"],
                    "code" => Response::HTTP_BAD_REQUEST
                ]);
            }
            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception' => $e->getMessage(),
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
    }

    /**
     * Permet de savoir si a un utilisateur a le doit d'utiliser les methodes http
     *
     * @param User $user
     * @param string|null $method
     * @return JsonResponse|null
     */
    public function ressourceAcces(User $user, ?string $method = null): ?JsonResponse
    {
        try {
            if (empty($method) || in_array(User::ROLE_ADMIN, $user->getRoles())) {
                return null;
            }
            $method = strtoupper($method);
            $access = $user->getAccess();
            switch ($method) {
                case "POST":

                    if ($access > User::ACCESS_CREATE) {
                        return new JsonResponse([
                            'exception' => "contact the administrator, your rights are not sufficient",
                            'errors' => ['user' => "accees insufisant $access", "method" => $method],
                            "code" => Response::HTTP_BAD_REQUEST
                        ]);
                    }
                    break;
                case "PUT":
                    if ($access >  User::ACCESS_UPDATE) {
                        return new JsonResponse([
                            'exception' => "contact the administrator, your rights are not sufficient",
                            'errors' => ['user' => "accees insufisant $access", "method" => $method],
                            "code" => Response::HTTP_BAD_REQUEST
                        ]);
                    }
                    break;
                case "DELETE":
                    if ($access >  User::ACCESS_ALL) {
                        return new JsonResponse([
                            'exception' => "contact the administrator, your rights are not sufficient",
                            'errors' => ['user' => "accees insufisant $access", "method" => $method],
                            "code" => Response::HTTP_BAD_REQUEST
                        ]);
                    }
                    break;
                default:
                    return null;
            }

            return null;
        } catch (\Exception $e) {
            return new JsonResponse([
                'exception' => $e->getMessage(),
                "code" => Response::HTTP_BAD_REQUEST
            ]);
        }
    }
}
