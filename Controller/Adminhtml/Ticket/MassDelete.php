<?php
/**
 * Created by PhpStorm.
 * User: inchoo
 * Date: 5/15/19
 * Time: 11:45 AM
 */

namespace Inchoo\Ticket\Controller\Adminhtml\Ticket;

use Inchoo\Ticket\Api\Data\TicketInterface;
use Inchoo\Ticket\Api\TicketRepositoryInterface;
use Magento\Backend\App\Action;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Inchoo_Ticket::ticket';

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;
    /**
     * @var \Inchoo\Ticket\Model\ResourceModel\Ticket\CollectionFactory
     */
    private $ticketCollectionFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param \Inchoo\Ticket\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        TicketRepositoryInterface $ticketRepository,
        \Inchoo\Ticket\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
    ) {
        parent::__construct($context);
        $this->ticketRepository = $ticketRepository;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
    }

    /**
     * Deletes all selected tickets and returns to ticket index
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     */
    public function execute()
    {
        $message = null;
        $data = $this->getRequest()->getParam('selected');
        if (empty($data)) {
            return $this->_redirect('ticket/ticket/');
        }

        $allTickets = $this->ticketCollectionFactory->create()
            ->addFieldToFilter(
                TicketInterface::TICKET_ID,
                ['ticket_id', $data]
            );
        foreach ($allTickets->getItems() as $ticket) {
            try {
                $this->ticketRepository->delete($ticket);
            } catch (\Exception $e) {
                return $message = $e->getMessage();
            }
        }

        if ($message === true) {
            $this->messageManager->addSuccessMessage('Tickets closed');
        } elseif ($message !== null and $message !== true) {
            $this->messageManager->addErrorMessage($message);
        }

        return $this->_redirect('ticket/ticket/');
    }
}
