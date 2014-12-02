<?php

namespace Matthias\SymfonyConsoleForm\Console\EventListener;

use Matthias\SymfonyConsoleForm\Console\Command\FormBasedCommand;
use Matthias\SymfonyConsoleForm\Console\Input\InputDefinitionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;

class SetInputDefinitionOfFormBasedCommandEventListener
{
    private $inputDefinitionFactory;

    public function __construct(InputDefinitionFactory $inputDefinitionFactory)
    {
        $this->inputDefinitionFactory = $inputDefinitionFactory;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        if ($command instanceof HelpCommand) {
            $command = $this->getCommandFromHelpCommand($command, $event->getInput());
        }

        if (!($command instanceof FormBasedCommand)) {
            return;
        }

        $this->createAndSetInputDefinition($command, $event->getInput());
    }

    private function createAndSetInputDefinition(Command $command, InputInterface $input)
    {
        $inputDefinition = $this->inputDefinitionFactory->createForCommand($command);

        $command->setDefinition($inputDefinition);
        $command->mergeApplicationDefinition();
        $input->bind($inputDefinition);
    }

    /**
     * @return Command|null
     */
    private function getCommandFromHelpCommand(HelpCommand $helpCommand)
    {
        // hackish way of retrieving the command for which help was asked
        $reflectionObject = new \ReflectionObject($helpCommand);
        $commandProperty = $reflectionObject->getProperty('command');
        $commandProperty->setAccessible(true);

        return $commandProperty->getValue($helpCommand);
    }
}