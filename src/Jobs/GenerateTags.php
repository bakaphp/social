<?php
declare(strict_types=1);

namespace Kanvas\Social\Jobs;

use Baka\Contracts\Auth\UserInterface;
use Baka\Contracts\Queue\QueueableJobInterface;
use Baka\Jobs\Job;
use Baka\Queue\Queue;
use Kanvas\Social\Models\Messages;
use Kanvas\Social\Models\MessageTags;
use Kanvas\Social\Models\Tags;
use Kanvas\Social\Services\Distributions;
use Kanvas\Social\Utils\StringFormatter;
use Phalcon\Di;
use Phalcon\Utils\Slug;

class GenerateTags extends Job implements QueueableJobInterface
{
    protected UserInterface $user;
    protected Messages $message;

    /**
     * Construct.
     *
     * @param UsersInteractions $user
     * @param Messages $message
     */
    public function __construct(UserInterface $user, Messages $message)
    {
        Queue::setDurable(true);

        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Handle the Generate the tags of the message.
     *
     * @return bool
     */
    public function handle() : bool
    {
        $tags = StringFormatter::getHashtagToString($this->message->message);

        foreach ($tags as $tag) {
            $tagData = Tags::findFirstOrCreate(
                [
                    'conditions' => 'slug = :tag_slug: AND is_deleted = 0',
                    'bind' => [
                        'tag_slug' => Slug::generate($tag)
                    ]
                ],
                [
                    'name' => $tag,
                    'slug' => Slug::generate($tag),
                    'users_id' => $this->user->getId(),
                    'apps_id' => $this->user->getDefaultCompany()->getId(),
                    'companies_id' => Di::getDefault()->get('app')->getId(),
                ]
            );

            MessageTags::findFirstOrCreate(
                [
                    'conditions' => 'message_id = :messageId: AND tags_id = :tagId: AND is_deleted = 0',
                    'bind' => [
                        'messageId' => $this->message->getId(),
                        'tagId' => $tagData->getId()
                    ]
                ],
                [
                    'message_id' => $this->message->getId(),
                    'tags_id' => $tagData->getId()
                ]
            );

            Distributions::sendToUsersFeeds($this->message, $tagData);
        }

        Di::getDefault()->get('log')->info('Generate tags for message ' . $this->message->getId());

        return true;
    }
}
