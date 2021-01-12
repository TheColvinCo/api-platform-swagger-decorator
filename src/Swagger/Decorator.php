<?php

declare(strict_types=1);

namespace Colvin\Swagger;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;

final class Decorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new ArrayObject([
                                                'type' => 'object',
                                                'properties' => [
                                                    'token' => [
                                                        'type' => 'string',
                                                        'readOnly' => true,
                                                    ],
                                                ],
                                            ]);
        $schemas['Credentials'] = new ArrayObject([
                                                      'type' => 'object',
                                                      'properties' => [
                                                          'email' => [
                                                              'type' => 'string',
                                                              'example' => 'johndoe@example.com',
                                                          ],
                                                          'password' => [
                                                              'type' => 'string',
                                                              'example' => 'apassword',
                                                          ],
                                                      ],
                                                  ]);

        $jwtPath = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                     operationId: 'postCredentialsItem',
                     responses: [
                                      '200' => [
                                          'description' => 'Get JWT token',
                                          'content' => [
                                              'application/json' => [
                                                  'schema' => [
                                                      '$ref' => '#/components/schemas/Token',
                                                  ],
                                              ],
                                          ],
                                      ],
                                  ],
                     summary: 'Get JWT token to login.',
                     requestBody: new Model\RequestBody(
                                      description: 'Generate new JWT Token',
                                      content: new ArrayObject([
                                                                   'application/json' => [
                                                                       'schema' => [
                                                                           '$ref' => '#/components/schemas/Credentials',
                                                                       ],
                                                                   ],
                                                               ]),
                                  ),
                 ),
        );
        $openApi->getPaths()->addPath('/authentication_token', $jwtPath);

        $schemas['RefreshToken'] = new ArrayObject([
                                                      'type' => 'object',
                                                      'properties' => [
                                                          'refresh_token' => [
                                                              'type' => 'string',
                                                              'example' => 'xxxx4b54b0076d2fcc5a51a6e60c0fb83b0bc90b47e2c886accb70850795fb311973c9d101fa0111f12eec739db063ec09d7dd79331e3148f5fc6e9cb362xxxx',
                                                          ],
                                                      ],
                                                  ]);

        $refreshTokenPath = new Model\PathItem(
            ref: 'Refresh Token',
            post: new Model\Operation(
                     operationId: 'postRefreshTokenItem',
                     responses: [
                                      '200' => [
                                          'description' => 'Refresh the token',
                                          'content' => [
                                              'application/json' => [
                                                  'schema' => [
                                                      '$ref' => '#/components/schemas/Token',
                                                  ],
                                              ],
                                          ],
                                      ],
                                  ],
                     summary: 'Refresh the token.',
                     requestBody: new Model\RequestBody(
                                      description: 'Generate new JWT Token',
                                      content: new ArrayObject([
                                                                   'application/json' => [
                                                                       'schema' => [
                                                                           '$ref' => '#/components/schemas/Credentials',
                                                                       ],
                                                                   ],
                                                               ]),
                                  ),
                 ),
        );

        $openApi->getPaths()->addPath('/refresh_token', $refreshTokenPath);

        return $openApi;
    }
}
