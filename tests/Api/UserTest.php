<?php


namespace App\Tests\Api;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends WebTestCase
{
    // TODO : creer une base sqlite pour les tests

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testPostCreateUser(): void
    {
        $data = [
            'email' => 'micklouzet3@dev.io',
            'username' => 'micklouzet3@dev.io',
            'password' => 'password',
            'firstName' => 'mick',
            'lastName' => 'louzet',
            'salt' => null
        ];

        $response = $this->request('POST', 'http://127.0.0.1:8000/api/users', $data);
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGetListUsers(): void
    {
        $response = $this->request('GET', 'http://127.0.0.1:8000/api/users');
        var_dump($response->getContent());
        $json = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/ld+json; charset=utf-8', $response->headers->get('Content-Type'));

        $this->assertArrayHasKey('hydra:totalItems', $json);

        $this->assertArrayHasKey('hydra:member', $json);
    }
    /**
     * @param string $method
     * @param string $uri
     * @param string|array|null $content
     * @param array $headers
     * @return Response
     */
    protected function request(string $method, string $uri, $content = null, array $headers = []): Response
    {
        $server = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'content-type') {
                $server['CONTENT_TYPE'] = $value;

                continue;
            }

            $server['HTTP_'.strtoupper(str_replace('-', '_', $key))] = $value;
        }

        if (is_array($content) && false !== preg_match('#^application/(?:.+\+)?json$#', $server['CONTENT_TYPE'])) {
            $content = json_encode($content);
        }

        $this->client->request($method, $uri, [], [], $server, $content);

        return $this->client->getResponse();
    }
}