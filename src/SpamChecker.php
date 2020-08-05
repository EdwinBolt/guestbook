<?php

namespace App;

use App\Entity\Comment;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker{
    private $client;
    private $endpoint;


    public function __construct(HttpClientInterface $client, string $akismetKey)
    {
        $this->client = $client;
        $this->endpoint = sprintf('http://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    /**
     * @return int Spam score: 0= not spam, 1= maybe span, 2= blatant spam
     *
     * @throws \RuntimeException if the call dit not work
     */

    public function getSpamScore(Comment $comment, array $context):int
    {
        $response = $this->client-> request('POST', $this->endpoint, [
            'body'=> array_merge($context,[
                'http://guestbook.example.com',
                'comment_type'=>'comment',
                'comment_author'=>$comment->getAuthor(),
                'comment_author_email'=>$comment->getEmail(),
                'comment_content'=>$comment->getText(),
                'comment_data_gmt'=>$comment->getCreatedAt()->format('c'),
                'blog_lang'=>'nl',
                'blog_charset'=>'UTF-8',
                'is_test'=>true,
            ] ),
        ]);



        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')){
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-pro-tip'][0])){
            throw new \RuntimeException(sprintf('unable to chekc for spam: %s (%s).', $content, $headers['x-akismet-pro-tip'][0]));
        }

        return 'true' === $content ? 1:0;

    }


}
