<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;
use Html;
use Illuminate\Support\Facades\URL;

use Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        // condition 1
        // if (env('APP_ENV') === 'production') {
        //     //URL::forceScheme('https');
        //     $httphost = Request::root();
        //     if($httphost=="http://sicerdas.web.id"){
        //         $httphost = "https://sicerdas.web.id";
        //         URL::forceScheme('https');
        //     }

        //     if($httphost=="http://www.sicerdas.web.id"){
        //         $httphost = "https://www.sicerdas.web.id";
        //         URL::forceScheme('https');
        //     }
        // }
        //
        Form::component('bsOpen', 'components.form.form-open', ['id'=>'','url'=>'']);
        Form::component('bsSubmit', 'components.form.submit-button', ['title'=>'']);
        Form::component('bsHidden', 'components.form.hidden-field', ['name'=>'','default'=>'']);
        Form::component('bsTextArea', 'components.form.textarea', ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsTextField', 'components.form.textfield', ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsCheckSwitch', 'components.form.check-switch', ['label'=>'','name'=>'','value'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsMoney', 'components.form.textfield-money', ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsDecimal', 'components.form.textfield-decimal', ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsNumeric', 'components.form.numerik', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
        Form::component('bsDatePicker', 'components.form.textfield-datepicker', ['label'=>'','name'=>'','default'=>'',
            'required'=>'','md_size'=>'md-8']);
         
        Form::component('bsEmail', 'components.form.textfield-email', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsReadOnly', 'components.form.textfield-readonly', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsPassword', 'components.form.textfield-password', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'', 'md_size'=>'md-4']);
        Form::component('bsDate', 'components.form.date', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsDateMask', 'components.form.date-mask', 
            ['label'=>'','name'=>'','default'=>'',
            'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsSelect2', 'components.form.select', 
            ['label', 'name', 'data'=>[], 'value'=>'',
                'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsSelect2Crypt', 'components.form.select-crypt', 
            ['label', 'name', 'data'=>[], 'value'=>'',
                'required'=>'', 'md_size'=>'md-8']);
        Form::component('bsRadionInline', 'components.form.radio-inline',
            ['label','name','data'=>[], 'value'=>'','required'=>false]);
        Form::component('bsClose', 'components.form.form-close',[]);
        //GENERATE MODAL
         Html::component('btnModal', 'components.modal.btn', 
            ['label'=>'','target'=>'','class'=>'primary']);
         Html::component('mOpen', 'components.modal.modal-open', ['id'=>'','title'=>'']);
         Html::component('mOpenMD', 'components.modal.modal-open-md', ['id'=>'','title'=>'']);
         Html::component('mOpenLG', 'components.modal.modal-open-lg', ['id'=>'','title'=>'']);
         Html::component('mCloseSubmitLG', 'components.modal.modal-close-lg-submit',['submit'=>'Submit']);
         Html::component('mCloseLG', 'components.modal.modal-close-lg',[]);

         Html::component('mClose', 'components.modal.modal-close',[]);
         Html::component('mCloseSubmit', 'components.modal.modal-close-submit',['submit'=>'Submit']);
         
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
