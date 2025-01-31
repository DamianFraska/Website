<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\UserController;

use App\DataFixtures\User\AdminUserFixture;
use App\DataFixtures\User\RegularUserFixture;
use App\Repository\User\UserRepository;
use App\Test\Enum\RouteEnum;
use App\Test\Traits\DataProvidersTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @covers \App\Controller\UserController
 */
final class DeleteActionTest extends WebTestCase
{
    use DataProvidersTrait;

    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    /**
     * @test
     * @dataProvider registeredUsersDataProvider
     */
    public function deleteAction_anonymousUser_returnsRedirectResponse(string $userId): void
    {
        $subjectUser = $this->userRepository->find($userId);

        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, $subjectUser->getId()));

        $this::assertResponseRedirects(RouteEnum::SECURITY_CONNECT_DISCORD, Response::HTTP_FOUND);
    }

    /**
     * @test
     * @dataProvider registeredUsersDataProvider
     */
    public function deleteAction_unauthorizedUser_returnsForbiddenResponse(string $userId): void
    {
        $user = $this->userRepository->find(RegularUserFixture::ID);
        $subjectUser = $this->userRepository->find($userId);

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, $subjectUser->getId()));

        $this::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function deleteAction_authorizedUser_returnsRedirectResponse(): void
    {
        $user = $this->userRepository->find(AdminUserFixture::ID);
        $subjectUser = $this->userRepository->find(RegularUserFixture::ID);

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, $subjectUser->getId()));

        $this::assertResponseRedirects(RouteEnum::USER_LIST);

        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, $subjectUser->getId()));

        $this::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function deleteAction_authorizedCurrentUser_returnsForbiddenResponse(): void
    {
        $user = $this->userRepository->find(AdminUserFixture::ID);
        $subjectUser = $user;

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, $subjectUser->getId()));

        $this::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function deleteAction_authorizedUser_returnsNotFoundResponse(): void
    {
        $user = $this->userRepository->find(AdminUserFixture::ID);

        $this->client->loginUser($user);
        $this->client->request(Request::METHOD_GET, sprintf(RouteEnum::USER_DELETE, 'non-existing-id'));

        $this::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
