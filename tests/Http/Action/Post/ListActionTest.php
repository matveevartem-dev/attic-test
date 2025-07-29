<?php

declare(strict_types=1);

namespace Tests\Http\Action\Post;

use App\Http\Action\Post\ListAction;
use App\Model\Post\PostRepository;
use App\Infrastructure\Core\Container;
use Dotenv\Dotenv;
use HttpSoft\Basis\Response\PrepareJsonDataTrait;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

class ListActionTest extends TestCase
{
    use PrepareJsonDataTrait;

    private Container $container;
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();

        Dotenv::createImmutable(__DIR__ . '/../../../../')->load();
        $container = new Container();
        $this->postRepository = $container->get(PostRepository::class);
    }

    /**
     * @return array
     */
    public function validPostData(): array
    {
        return [
            [
                'id' => 1,
                'userId' => 1,
                'title' => "sunt aut facere repellat provident occaecati excepturi optio reprehenderit",
                'body' => "quia et suscipit\nsuscipit recusandae consequuntur expedita et cum\nreprehenderit molestiae ut ut quas totam\nnostrum rerum est autem sunt rem eveniet architecto",
            ],
            [
                "id" => 7,
                "userId" => 1,
                "title" => "magnam facilis autem",
                "body" => "dolore placeat quibusdam ea quo vitae\nmagni quis enim qui quis quo nemo aut saepe\nquidem repellat excepturi ut quia\nsunt ut sequi eos ea sed quas",
            ],
            [
                "id" => 17,
                "userId" => 2,
                "title" => "fugit voluptas sed molestias voluptatem provident",
                "body" => "eos voluptas et aut odit natus earum\naspernatur fuga molestiae ullam\ndeserunt ratione qui eos\nqui nihil ratione nemo velit ut aut id quo",
            ],
        ];
    }

    /**
     * @param array|null $posts
     */
    public function testHandle(): void
    {
        $action = new ListAction($this->postRepository);
        $response = $action->handle(new ServerRequest());
        $expected = $this->prepareJsonData((string) (new JsonResponse($this->validPostData()))->getBody());
        $body = json_decode($this->prepareJsonData((string) $response->getBody()), true);
        $returned = $this->prepareJsonData([$body[0],$body[6], $body[16]]);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($expected, json_encode($returned));
    }
}
