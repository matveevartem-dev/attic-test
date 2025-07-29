<?php

declare(strict_types=1);

namespace Tests\Http\Action\Post;

use App\Http\Action\Post\ViewAction;
use App\Model\Post\PostRepository;
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

    private int $postId = 1;

    private Container $container;
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();

        Dotenv::createImmutable(__DIR__ . '/../../../../')->load();
        $container = new Container();
        $this->postRepository = $container->get(PostRepository::class);
    }

    public function validPostData(): array
    {
        return [
            'id' => 1,
            'userId' => 1,
            'title' => "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
            'body' => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto"
        ];
    }

    public function testHandle()
    {
        $action = new ViewAction($this->postRepository);
        $response = $action->handle((new ServerRequest())->withAttribute('id', $this->postId));
        $expected = $this->prepareJsonData((string) (new JsonResponse($this->validPostData()))->getBody());
        $this->assertSame($expected, (string) $response->getBody());
    }

    /**
     * @param int $postId
     */
    public function testHandleThrowsPostNotFoundException(): void
    {
        $request = (new ServerRequest())->withAttribute('id', -1);
        $this->expectException(NotFoundHttpException::class);
        $this->assertTrue(true);
        (new ViewAction($this->postRepository))->handle($request);
    }
}
