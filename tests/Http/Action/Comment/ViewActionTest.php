<?php

declare(strict_types=1);

namespace Tests\Http\Action\Comment;

use App\Http\Action\Comment\ViewAction;
use App\Model\Comment\CommentRepository;
use App\Infrastructure\Core\Container;
use Dotenv\Dotenv;
use HttpSoft\Basis\Exception\NotFoundHttpException;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

class ViewActionTest extends TestCase
{
    use PrepareJsonDataTrait;

    private int $commentId = 1;

    private Container $container;
    private CommentRepository $commentRepository;

    public function __construct()
    {
        parent::__construct();

        Dotenv::createImmutable(__DIR__ . '/../../../../')->load();
        $container = new Container();
        $this->commentRepository = $container->get(CommentRepository::class);
    }

    public function validPostData(): array
    {
        return [
            "id" => 1,
            "postId" => 1,
            "email" => "Eliseo@gardner.biz",
            "name" => "id labore ex et quam laborum",
            "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
        ];
    }

    public function testHandle()
    {
        $action = new ViewAction($this->commentRepository);
        $response = $action->handle((new ServerRequest())->withAttribute('id', $this->commentId));
        $expected = $this->prepareJsonData((string) (new JsonResponse($this->validPostData()))->getBody());
        $this->assertSame($expected, (string) $response->getBody());
    }

    /**
     * @param int $commentId
     */
    public function testHandleThrowsPostNotFoundException(): void
    {
        $request = (new ServerRequest())->withAttribute('id', -1);
        $this->expectException(NotFoundHttpException::class);
        $this->assertTrue(true);
        (new ViewAction($this->commentRepository))->handle($request);
    }
}
