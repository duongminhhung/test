<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MyInput extends Component
{
    public $name;
    public $myClass;
    public $value;
    public $read;
    public $col0;
    public $col1;
    public $myId;
    public $idMess;
    public $idShow;
    public $message;
    public $showMessage;
    public $place;
    public $myType;
    public $myName;
    public $required;
    public $step;
    public $min;
    public $max;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $name     = '', 
        $myClass  = '', 
        $col0     = '', 
        $col1     = '',
        $myId     = '', 
        $message  = '', 
        $idMess   = '', 
        $read     = true,
        $value    = 0,
        $place    = '',
        $myType   = 'text',
        $myName   = '',
        $required = false,
        $step     = '',
        $min      = '',
        $max      = ''
    ){
        $this->name        = $name;
        $this->myClass     = $myClass;
        $this->col0        = $col0;
        $this->col1        = $col1;
        $this->myId        = $myId;
        $this->idMess      = $idMess;
        $this->message     = $message;
        $this->showMessage = false;
        $this->read        = $read;
        $this->value       = $value;
        $this->idShow      = '';
        $this->place       = $place;
        $this->myType      = $myType;
        $this->myName      = $myName;
        $this->required    = $required;
        $this->step        = $step;
        $this->min         = $min;
        $this->max         = $max;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $this->showMess();
        $this->showId();

        return view('components.my-input');
    }

    private function showMess()
    {
        if ($this->message != '') 
        {
            $this->showMessage = true;
        }
    }

    private function showId()
    {
        if ($this->myId != '') 
        {
            $this->idShow = 'id='.$this->myId;
        }
    }
}
