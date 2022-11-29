<?php

namespace Kanvas\Social\Tests\Integration\Social\Service;

use IntegrationTester;
use Kanvas\Social\Follow;
use Kanvas\Social\Tags;
use Kanvas\Social\Test\Support\Models\Tag;
use Kanvas\Social\Test\Support\Models\Users;

class FollowsCest
{


    /**
     * Test follow service method user follow.
     *
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function follow(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);
        $userFollow = Users::findFirst(-1);

        for ($i = 1; $i < 10; $i++) {
            $tag1 = Tags::create(Users::findFirst(1), 'Tag for test');
            if (!$user->isFollowing($tag1)) {
                $I->assertTrue($user->follow($tag1));
            }
        }

        if (!$userFollow->isFollowing($user)) {
            $userFollow->follow($user);
        }
    }

    /**
     * Test Users follows by entities.
     *
     * @param IntegrationTester $I
     *
     * @return void
     */
    public function getUserFollows(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);
        $user->id = 1;

        $tag1 = Tags::create(Users::findFirst(1), 'Tag for test');
        $follows = Follow::getFollowsByUser($user, $tag1)->toArray();
        $I->assertGreaterThan(0, $follows);
        $I->assertNotNull($follows[0]['id']);

        $I->assertEquals($follows[0]['entity_namespace'], get_class($tag1));
    }

    public function unfollow(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);

        for ($i = 1; $i < 5; $i++) {
            $userFollow = Users::findFirst(-1);
            $userFollow->id = $i + 1;

            if ($user->isFollowing($userFollow)) {
                $I->assertTrue($user->unFollow($userFollow));
            }
        }
    }

    public function isFollowing(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);

        for ($i = 1; $i < 5; $i++) {
            $userFollow = Users::findFirst(1);
            $userFollow->id = $i + 1;

            $I->assertIsBool($user->isFollowing($userFollow));
        }
    }

    public function followingCount(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);

        $I->assertGreaterThan(1, $user->getTotalFollowing(Users::class));
    }

    public function followersCount(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);

        $I->assertGreaterThan(0, $user->getTotalFollowers());
    }

    public function followingCountOfEntity(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);

        $I->assertGreaterThan(1, $user->getTotalFollowing(Tag::class));
    }

    public function followersCountOfEntity(IntegrationTester $I) : void
    {
        $user = Users::findFirst(1);
        $tag = new Tag();
        $tag->id = 1;

        $I->assertGreaterThan(0, Follow::getTotalFollowers($tag));
    }
}
