<?php


namespace App\Http\Controllers\Admin;

use App\Entities\Faq;
use App\Entities\FaqType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;


class FaqCrudController extends CrudController
{

    public function setup()
    {
        $this->crud->setModel(Faq::class);
        $this->crud->setRoute('admin/faq');
        $this->crud->setEntityNameStrings('FAQ', 'FAQs');


        $this->crud->setColumns([
            [
                'label' => 'Category',
                'type' => 'select',
                'name' => 'faq_type_id',
                'entity' => 'type',
                'attribute' => 'name',
                'model' => FaqType::class,
            ],

            [
                'name'  => 'question',
                'label' => 'Question',
                'type'  => 'text',
            ],

            [
                'name'  => 'answer',
                'label' => 'Answer',
                'type'  => 'text',
            ],
        ]);


        $this->crud->addField([
            'label' => 'Category',
            'type' => 'select',
            'name' => 'faq_type_id',
            'entity' => 'type',
            'attribute' => 'name',
            'model' => FaqType::class,
        ]);


        $this->crud->addField([
            'name'       => 'original_question',
            'label'      => 'Question (english)',
            'type'       => 'text',
        ]);

        $this->crud->addField([
            'name'       => 'original_answer',
            'label'      => 'Answer (english)',
            'type'       => 'textarea',
        ]);

        $this->crud->addField([
            'name'       => 'question_de',
            'label'      => 'Question (german)',
            'type'       => 'text',
        ]);

        $this->crud->addField([
            'name'       => 'answer_de',
            'label'      => 'Answer (german)',
            'type'       => 'textarea',
        ]);


    }

    public function store(Request $request)
    {
        $this->amendRequest($request);
        return parent::storeCrud();
    }

    public function update(Request $request)
    {
        $this->amendRequest($request);
        return parent::updateCrud();
    }

    /**
     * Replace a model accessor field by the original field.
     *
     * @param Request $request
     */
    private function amendRequest(Request $request): void
    {
        $request->merge([
            'answer' => $request->get('original_answer'),
            'question' => $request->get('original_question')
        ]);
    }

}