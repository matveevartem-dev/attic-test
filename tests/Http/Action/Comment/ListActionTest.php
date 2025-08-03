<?php

declare(strict_types=1);

namespace Tests\Http\Action\Post;

use App\Http\Action\Comment\ListAction;
use App\Model\Comment\CommentRepository;
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
    private CommentRepository $commentRepository;

    public function __construct()
    {
        parent::__construct();

        Dotenv::createImmutable(__DIR__ . '/../../../../')->load();
        $container = new Container();
        $this->commentRepository = $container->get(CommentRepository::class);
    }

    /**
     * @return array
     */
    public function validCommenttData(): array
    {
        return [
            [
                "id" => 1,
                "postId" => 1,
                "email" => "Eliseo@gardner.biz",
                "name" => "id labore ex et quam laborum",
                "body" => "laudantium enim quasi est quidem magnam voluptate ipsam eos\ntempora quo necessitatibus\ndolor quam autem quasi\nreiciendis et nam sapiente accusantium"
            ],
            [
                "id" => 2,
                "postId" => 1,
                "email" => "Jayne_Kuhic@sydney.com",
                "name" => "quo vero reiciendis velit similique earum",
                "body" => "est natus enim nihil est dolore omnis voluptatem numquam\net omnis occaecati quod ullam at\nvoluptatem error expedita pariatur\nnihil sint nostrum voluptatem reiciendis et"
            ],
            [
                "id" => 3,
                "postId" => 1,
                "email" => "Nikita@garfield.biz",
                "name" => "odio adipisci rerum aut animi",
                "body" => "quia molestiae reprehenderit quasi aspernatur\naut expedita occaecati aliquam eveniet laudantium\nomnis quibusdam delectus saepe quia accusamus maiores nam est\ncum et ducimus et vero voluptates excepturi deleniti ratione"
            ]
        ];
    }

    /**
     * @param array|null $posts
     */
    public function testHandle(): void
    {
        $action = new ListAction($this->commentRepository);
        $response = $action->handle(new ServerRequest());
        $expected = $this->prepareJsonData((string) (new JsonResponse($this->validCommenttData()))->getBody());
        $body = json_decode($this->prepareJsonData((string) $response->getBody()), true);
        $returned = $this->prepareJsonData([$body[0],$body[1], $body[2]]);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($expected, json_encode($returned));
    }
}
