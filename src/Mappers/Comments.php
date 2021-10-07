<?php

declare(strict_types=1);

namespace Kanvas\Social\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;

class Comments extends CustomMapper
{
    /**
     * @param Leads $lead
     * @param \Gewaer\Dto\Leads $leadDto
     *
     * @return leadDto
     */
    public function mapToObject($comment, $commentDto, array $context = [])
    {
        $currentUsers = $comment->users;
        $commentDto->id = (int)$comment->id;
        $commentDto->apps_id = $comment->apps_id;
        $commentDto->companies_id = $comment->companies_id;
        $commentDto->users = [
            'id' => $currentUsers->id,
            'firstname' => $currentUsers->firstname,
            'lastname' => $currentUsers->lastname,
            'photo' => $currentUsers->getPhoto() ?? null
        ];
        $commentDto->message_id = $comment->message_id;
        $commentDto->message = $comment->message;
        $commentDto->reactions_count = $comment->reactions_count;
        $commentDto->parent_id = $comment->parent_id;

        $commentDto->created_at = $comment->created_at;
        $commentDto->updated_at = $comment->updated_at;
        $commentDto->is_deleted = $comment->is_deleted;

        return $commentDto;
    }
}
