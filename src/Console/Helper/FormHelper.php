<?php

namespace Matthias\SymfonyConsoleForm\Console\Helper;

use Matthias\SymfonyConsoleForm\Bridge\FormFactory\ConsoleFormFactory;
use Matthias\SymfonyConsoleForm\Bridge\Interaction\FormInteractor;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormInterface;

class FormHelper extends Helper
{
    private $formFactory;
    private $formInteractor;

    public function getName()
    {
        return 'form';
    }

    public function __construct(
        ConsoleFormFactory $formFactory,
        FormInteractor $formInteractor
    ) {
        $this->formFactory = $formFactory;
        $this->formInteractor = $formInteractor;
    }

    public function interactUsingForm($formType, InputInterface $input, OutputInterface $output, array $options = [])
    {
        $form = $this->formFactory->create($formType, $input, $options);

        $submittedData = $this->formInteractor->interactWith($form, $this->getHelperSet(), $input, $output);

        $form->submit($submittedData);
        if (!$form->isValid()) {
            $this->invalidForm($form);
        }

        return $form->getData();
    }

    private function invalidForm(FormInterface $form)
    {
        throw new \RuntimeException(sprintf('Invalid data provided: %s', $form->getErrors(true, false)));
    }
}
