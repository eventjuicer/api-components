<?php

namespace Eventjuicer\Services\MillenetCsv\Http;

use Kris\LaravelFormBuilder\Form;

class UploadFileForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('name', 'text')
            ->add('file', 'file')
            ->add("submit", "submit");
    }
}
