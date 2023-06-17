<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Filter extends Component
{
    public $id, $fil, $dat, $key, $name, $search, $searchReplace, $nameSelect = [];
    public $from, $to = 'true';
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $id            = [], // Dạng tìm kiếm (select hay input)
        $fil           = [], // Dạng tìm kiếm (select hay input)
        $dat           = [], // Dữ liệu
        $key           = [], // Khóa chính
        $name          = [], // Tên chỉ mục
        $nameSelect    = [], // Tên chỉ mục
        $search        = [], // Khóa tìm kiếm
        $searchReplace = [], // Khóa tìm kiếm thay thế
        $from          = true, // Bật tìm kiếm theo thời gian bắt đầu
        $to            = true  // Bật tìm kiếm theo thời gian kết thúc
    ){
        $this->id            = $id;
        $this->fil           = $fil;
        $this->dat           = $dat;
        $this->key           = $key;
        $this->name          = $name;
        $this->nameSelect    = $nameSelect;
        $this->search        = $search;
        $this->searchReplace = $searchReplace;
        $this->from          = $from;
        $this->to            = $to;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.filter');
    }
}
