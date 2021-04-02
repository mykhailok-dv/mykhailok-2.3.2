<?php

namespace Mykhailok\SupportChat\Console\Command;

class ChatMessageSampleData extends \Symfony\Component\Console\Command\Command
{
    public const CHAT_COUNT_OPTION_NAME = 'count';
    public const CHAT_COUNT_OPTION_DEFAULT = 20;

    private \Mykhailok\SupportChat\Model\ChatFactory $chatModelFactory;
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageModelFactory;
    private \Psr\Log\LoggerInterface $logger;
    private \Magento\Framework\App\State $appState;
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    public function __construct(
        \Mykhailok\SupportChat\Model\ChatFactory $chatModelFactory,
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageModelFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->chatModelFactory = $chatModelFactory;
        $this->chatMessageModelFactory = $chatMessageModelFactory;
        $this->logger = $logger;
        $this->appState = $appState;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('my:chat:gen-sample-data');
        $this->setDescription('The command generates some chat messages with sample data.');
        $this->addOption(
            self::CHAT_COUNT_OPTION_NAME,
            null,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'The count of generated entities.'
        );

        parent::configure();
    }

    /**
     * CLI command description
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     * @throws \Exception
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {
        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            [$this, 'generateChatMessage'],
            [$input, $output]
        );
    }

    public function generateChatMessage(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {
        $messageWords = ['Lorem', 'Ipsum', 'is', 'simply', 'dummy', 'text'];
        $authorNames = ['Liam', 'Olivia', 'Noah', 'Emma', 'Oliver', 'Ava', 'William', 'Sophia'];

        try {
            /** @var \Magento\Framework\DB\Transaction $transaction */
            $transaction = $this->transactionFactory->create();
            $newChatModels = [];
            $chatCount = $input->getOption(self::CHAT_COUNT_OPTION_NAME) ?? self::CHAT_COUNT_OPTION_DEFAULT;

            for ($i = 0; $i < $chatCount; $i++) {
                $chat = $this->chatModelFactory->create();
                $chat
                    ->setHash(uniqid())
                    ->setPriority(array_rand([
                        \Mykhailok\SupportChat\Model\Chat::REGULAR_PRIORITY,
                        \Mykhailok\SupportChat\Model\Chat::WAITING_PRIORITY,
                        \Mykhailok\SupportChat\Model\Chat::IMMEDIATE_PRIORITY,
                    ]))
                    ->setWebsiteId(1);
                $transaction->addObject($chat);
                $newChatModels[$chat->getHash()] = $chat;
            }
            $transaction->save();

            $transaction = $this->transactionFactory->create();

            while (!empty($newChatModels)) {
                shuffle($messageWords);
                $message = implode(' ', $messageWords);
                $authorName = $authorNames[array_rand($authorNames)];

                $chat = array_shift($newChatModels);
                $chatMessage = $this->chatMessageModelFactory->create();
                $chatMessage
                    ->setMessage($message)
                    ->setAuthorName($authorName)
                    ->setAuthorType(\Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER)
                    ->setAuthorId(0)
                    ->setChatId($chat->getId());

                $transaction->addObject($chatMessage);
            }
            $transaction->save();
            $output->writeln('The chat sample data successfully created.');
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            $output->writeln('The chat sample data failed.');
        }
    }
}
