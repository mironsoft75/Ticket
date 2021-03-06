<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/15/19
 * Time: 1:24 PM
 */

namespace Inchoo\Ticket\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;

/**
 * Class Reply
 * @package Inchoo\Ticket\Controller\Adminhtml\Ticket
 */
class Reply extends Action
{
    const ADMIN_RESOURCE = 'Inchoo_Ticket::ticket';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Inchoo\Ticket\Model\TicketReplyFactory
     */
    private $ticketReplyFactory;
    /**
     * @var \Inchoo\Ticket\Model\ResourceModel\TicketReply
     */
    private $ticketReplyResource;
    /**
     * @var Session
     */
    private $authSession;
    /**
     * @var \Inchoo\Ticket\Api\TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * Reply constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Inchoo\Ticket\Model\TicketReplyFactory $ticketReplyFactory
     * @param \Inchoo\Ticket\Model\ResourceModel\TicketReply $ticketReplyResource
     * @param Session $authSession
     * @param \Inchoo\Ticket\Api\TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Inchoo\Ticket\Model\TicketReplyFactory $ticketReplyFactory,
        \Inchoo\Ticket\Model\ResourceModel\TicketReply $ticketReplyResource,
        Session $authSession,
        \Inchoo\Ticket\Api\TicketRepositoryInterface $ticketRepository
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->ticketReplyFactory = $ticketReplyFactory;
        $this->ticketReplyResource = $ticketReplyResource;
        $this->authSession = $authSession;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Creates reply and redirects back to the ticket detail
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     */
    public function execute()
    {
        $ticketId = (int) $this->request->getPostValue('ticket_id');
        $replyMessage = $this->request->getPostValue('reply');
        if (empty($replyMessage)) {
            $this->messageManager->addErrorMessage('Reply message empty!');
            return $this->_redirect('ticket/ticket/');
        }

        try {
            $reply = $this->ticketReplyFactory->create();
            $reply->setMessage($replyMessage);
            $reply->setTickedId($ticketId);
            $reply->setAdminId((int)$this->authSession->getUser()->getId());
            $this->ticketReplyResource->save($reply);
            $this->messageManager->addSuccessMessage('Replied!');
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage('Reply not sent!');
        }

        return $this->_redirect('ticket/ticket/details/id/', ['id' => $ticketId]);
    }
}
