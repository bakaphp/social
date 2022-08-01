<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

use Canvas\Enums\App;
use Canvas\Models\Behaviors\Uuid;
use Phalcon\Di;

class MessageTypes extends BaseModel
{
    public $id;
    public ?string $uuid = null;
    public $apps_id;
    public $languages_id;
    public $name;
    public $verb;
    public $template;
    public $templates_plura;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('message_types');

        $this->addBehavior(
            new Uuid()
        );

        $this->hasMany(
            'id',
            Messages::class,
            'message_types_id',
            [
                'reusable' => true,
                'alias' => 'messages',
                'params' => [
                    'conditions' => 'is_deleted = 0'
                ]
            ]
        );

        $this->hasMany(
            'id',
            AppModuleMessage::class,
            'message_types_id',
            [
                'reusable' => true,
                'alias' => 'appModules'
            ]
        );
    }

    /**
     * Return a Message object by its id.
     *
     * @param string $uuid
     *
     * @return self
     */
    public static function getByUuid(string $uuid) : self
    {
        return MessageTypes::findFirstOrFail([
            'conditions' => 'uuid = :uuid: and is_deleted = 0',
            'bind' => [
                'uuid' => $uuid
            ]
        ]);
    }

    /**
     * Get the message type by its verb.
     *
     * @param string $verb
     * @param UserInterface $user
     *
     * @return self
     */
    public static function getTypeByVerb(string $verb) : self
    {
        return MessageTypes::findFirstOrCreate(
            [
                'conditions' => 'verb = :verb: AND apps_id IN (:currentAppId:, :defaultApp:) AND is_deleted = 0',
                'bind' => [
                    'verb' => $verb,
                    'currentAppId' => Di::getDefault()->get('app')->getId(),
                    'defaultApp' => App::CORE_APP_ID
                ]
            ],
            [
                'verb' => $verb,
                'apps_id' => Di::getDefault()->get('app')->getId(),
                'name' => $verb,
                'languages_id' => 1
            ]
        );
    }
}
