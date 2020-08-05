<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class ConferenceControllerTest extends WebTestCase
{
    public function testIndex(){
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback');
    }



    public function testCommentSubmission(){
        $client = static::createClient();
        $client->request('GET','/conference/amsterdam-2019');
        $client->submitForm('Submit',[
            'comment_form[author]'=>'Edwin',
            'comment_form[text]'=>'Auto text voor test',
            'comment_form[email]'=>'auto@test.optiwise',
            'comment_form[photo]'=>dirname(__DIR__).'/public/images/construction.png',
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 5")');
    }



    public function testConferencePage(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h4'));
        $client->clickLink('View');

        $this->assertPageTitleContains('groningen');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2','groningen');
        $this->assertSelectorExists('div:contains("There are 1 comments")');

    }

}
