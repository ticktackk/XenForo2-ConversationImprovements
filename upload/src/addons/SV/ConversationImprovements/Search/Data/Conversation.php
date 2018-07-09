<?php

/*
 * This file is part of a XenForo add-on.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SV\ConversationImprovements\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\Data\AbstractData;
use XF\Search\IndexRecord;
use XF\Search\MetadataStructure;

/**
 * A search handler for conversations.
 */
class Conversation extends AbstractData
{
    /**
     * @param Entity $entity
     *
     * @return IndexRecord
     */
    public function getIndexData(Entity $entity)
    {
        /** @var \XF\Entity\ConversationMaster $entity */
        $firstMessage = $entity->FirstMessage;

        return IndexRecord::create('conversation', $entity->conversation_id, [
            'title'         => $entity->title_,
            'message'       => $firstMessage ? $firstMessage->message_ : '',
            'date'          => $entity->start_date,
            'user_id'       => $entity->user_id,
            'discussion_id' => $entity->conversation_id,
            'metadata'      => $this->getMetadata($entity)
        ]);
    }

    /**
     * @param \XF\Entity\ConversationMaster $entity
     */
    protected function getMetadata(\XF\Entity\ConversationMaster $entity)
    {
        return [
            'conversation' => $entity->conversation_id,
            'recipients'   => array_keys($entity->recipients)
        ];
    }

    /**
     * @param MetadataStructure $structure
     */
    public function setupMetadataStructure(MetadataStructure $structure)
    {
        $structure->addField('conversation', MetadataStructure::INT);
        $structure->addField('recipients', MetadataStructure::INT);
    }

    /**
     * @param Entity $entity
     *
     * @return int
     */
    public function getResultDate(Entity $entity)
    {
        /** @var \XF\Entity\ConversationMaster $entity */
        return $entity->start_date;
    }

    /**
     * @param Entity $entity
     * @param array  $options
     *
     * @return array
     */
    public function getTemplateData(Entity $entity, array $options = [])
    {
        return [
            'conversation' => $entity,
            'options'      => $options
        ];
    }

    /**
     * @param bool $forView
     *
     * @return array
     */
    public function getEntityWith($forView = false)
    {
        $with = ['FirstMessage'];

        if ($forView) {
            $with[] = 'Starter';

            $visitor = \XF::visitor();
            $with[] = "Users|{$visitor->user_id}";
        }

        return $with;
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
        return 'public:sv_convimprov_search_result_conversation';
    }

    /**
     * @param Entity $entity
     * @param string $error
     *
     * @return bool
     */
    public function canUseInlineModeration(Entity $entity, &$error = null)
    {
        return true;
    }
}