<?php

namespace GemeenteAmsterdam\HeelEnSchoonBundle\Util;

use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;

class FormErrorsToArray
{
    /**
     * @param FormErrorIterator $errors
     * @return array[]|string
     */
    public static function convert(FormErrorIterator $errors)
    {
        $errorList = [];
        foreach ($errors as $e) {
            /** @var $e FormError */
            $name = self::buildName($e->getOrigin());
            if (isset($errorList[$name]) === false) {
                $errorList[$name] = [];
            }
            $errorList[$name][] = $e->getMessage();
        }
        return $errorList;
    }

    protected static function buildName(Form $form, $name = '')
    {
        $name .= ($name !== '' ? '.' : '') . $form->getName();
        if ($form->getParent() !== null) {
            $name = self::buildName($form->getParent(), $name);
        }
        return $name;
    }
}