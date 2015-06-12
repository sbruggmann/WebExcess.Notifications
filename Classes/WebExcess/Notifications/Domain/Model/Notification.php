<?php
namespace WebExcess\Notifications\Domain\Model;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "WebExcess.Notifications". *
 *                                                                          *
 *                                                                          */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Notification {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $packageKey;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array of strings
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $targetUsers = array();

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPackageKey() {
        return $this->packageKey;
    }

    /**
     * @param string $packageKey
     * @return void
     */
    public function setPackageKey($packageKey) {
        $this->packageKey = $packageKey;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getTargetUsers() {
        return $this->targetUsers;
    }

    /**
     * @param array $targetUsers
     */
    public function setTargetUsers($targetUsers) {
        $this->targetUsers = $targetUsers;
    }

    /**
     * @param string $targetUser
     */
    public function addTargetUser($targetUser) {
        $this->targetUsers[] = $targetUser;
    }

    /**
     * @param string $targetUser
     * @return bool
     */
    public function hasTargetUser($targetUser) {
        return in_array($targetUser, $this->targetUsers);
    }

    /**
     * @param string $targetUser
     */
    public function removeTargetUser($targetUser) {
        unset($this->targetUsers[ array_search($targetUser, $this->targetUsers) ]);
    }

}