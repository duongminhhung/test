<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => ':Attribute는 허용되어야합니다..',
    'active_url'      => ':Attribute는 유효한 URL이 아닙니다.',
    'after'           => ':Attribute는 이후 날짜 여야합니다. :date.',
    'after_or_equal'  => ':Attribute 는 이후 날짜 여야합니다 또는 By :date.',
    'alpha'           => ':Attribute는 문자 만 포함 할 수 있습니다.',
    'alpha_dash'      => ':Attribute는 문자, 숫자, 대시 및 밑줄 만 포함 할 수 있습니다.',
    'alpha_num'       => ':Attribute는 문자와 숫자 만 포함 할 수 있습니다..',
    'array'           => ':Attribute는 Array입니다.',
    'before'          => ':Attribute 하루 전이어야합니다 :date.',
    'before_or_equal' => ':Attribute 하루 전이어야합니다 또는 By :date.',
    'between'         => 
    [
        'numeric' => ':Attribute 있는 :min và :max.',
        'file'    => ':Attribute 있다 :min và :max kilobytes.',
        'string'  => ':Attribute 있다 :min và :max 문자.',
        'array'   => ':Attribute 있다 :min và :max 품목.',
    ],
    'boolean'        => ':Attribute 결과는 참 또는 거짓이어야합니다..',
    'confirmed'      => ':Attribute 부적절한 식별.',
    'date'           => ':Attribute 유효한 날짜가 아닙니다..',
    'date_equals'    => ':Attribute 같은 날짜 여야합니다. :date.',
    'date_format'    => ':Attribute 형식과 일치하지 않습니다 :format.',
    'different'      => ':Attribute and :other 달라야합니다.',
    'digits'         => ':Attribute 필수 :digits 숫자.',
    'digits_between' => ':Attribute 최소값과 최대 값 사이 여야합니다..',
    'dimensions'     => ':Attribute 이미지 크기가 잘못되었습니다..',
    'distinct'       => ':Attribute 결과에 중복 된 값이 있습니다.',
    'email'          => ':Attribute 유효한 이메일 주소이어야합니다.',
    'ends_with'      => ':Attribute 다음 중 하나로 끝나야합니다 :values.',
    'exists'         => '속성:Attribute 선택은 유효하지 않습니다.',
    'file'           => ':Attribute 파일이어야합니다.',
    'filled'         => ':Attribute 결과에는 값이 있어야합니다..',
    'gt'             => 
    [
        'numeric' => ':Attribute 더 커야합니다 :value.',
        'file'    => ':Attribute 더 커야합니다 :value kilobytes.',
        'string'  => ':Attribute 더 커야합니다 :value 문자.',
        'array'   => ':Attribute 더 있어야합니다 :value 품목.',
    ],
    'gte' => 
    [
        'numeric' => ':Attribute 보다 크거나 같아야합니다. :value.',
        'file'    => ':Attribute 보다 크거나 같아야합니다. :value kilobytes.',
        'string'  => ':Attribute 보다 크거나 같아야합니다. :value 문자.',
        'array'   => ':Attribute 있어야한다 :value 품목 또는 보다.',
    ],
    'image'    => ':Attribute 이미지 여야합니다.',
    'in'       => 'Thuộc tính :Attribute 선택은 유효하지 않습니다.',
    'in_array' => ':Attribute 결과가 없습니다. :other.',
    'integer'  => ':Attribute 정수 여야합니다..',
    'ip'       => ':Attribute 유효한 IP 주소 여야합니다..',
    'ipv4'     => ':Attribute 유효한 IPv4 주소 여야합니다..',
    'ipv6'     => ':Attribute 유효한 IPv6 주소 여야합니다..',
    'json'     => ':Attribute 유효한 JSON 문자열이어야합니다..',
    'lt'       => 
    [
        'numeric' => ':Attribute 더 작아야합니다 :value.',
        'file'    => ':Attribute 더 작아야합니다 :value kilobytes.',
        'string'  => ':Attribute 더 작아야합니다 :value 문자.',
        'array'   => ':Attribute 더 적어야한다 :value 품목.',
    ],
    'lte' => 
    [
        'numeric' => ':Attribute 이보다 작거나 같아야합니다.:value.',
        'file'    => ':Attribute 이보다 작거나 같아야합니다. :value kilobytes.',
        'string'  => ':Attribute 이보다 작거나 같아야합니다. :value 문자.',
        'array'   => ':Attribute 더는있을 수 없어 :value 품목.',
    ],
    'max' => 
    [
        'numeric' => ':Attribute 더 크지 않을 수 있습니다 :max.',
        'file'    => ':Attribute 더 크지 않을 수 있습니다:max kilobytes.',
        'string'  => ':Attribute 더 크지 않을 수 있습니다 :max 문자.',
        'array'   => ':Attribute 더 많이 없을 수 있다:max 품목.',
    ],
    'mimes'     => ':Attribute 파일 형식이어야합니다.: :values.',
    'mimetypes' => ':Attribute 파일 형식이어야합니다.: :values.',
    'min'       => 
    [
        'numeric' => ':Attribute 최소한 min이어야합니다.',
        'file'    => ':Attribute 최소한 min kilobytes 이어야합니다:.',
        'string'  => ':Attribute 최소한 문자 min이어야합니다',
        'array'   => ':Attribute 최소한 최소 항목이 있어야합니다..',
    ],
    'not_in'               => '속성 :Attribute 잘못된 선택.',
    'not_regex'            => ':Attribute 잘못된 형식.',
    'numeric'              => ':Attribute 숫자 여야합니다.',
    'password'             => '잘못된 비밀번호.',
    'present'              => ':Attribute 결과가 면 있어야합니다. ',.',
    'regex'                => ':Attribute 잘못된 형식입니다..',
    'required'             => ':Attribute 필수 필드입니다..',
    'required_if'          => ':Attribute 필드는 다음과 같은 경우 필수입니다 :other is :value.',
    'required_unless'      => ':Attribute 필드는 다음과 같은 경우 필수입니다 :other trong :values.',
    'required_with'        => ':Attribute 필드는 다음과 같은 경우 필수입니다 :values 선물이다.',
    'required_with_all'    => ':Attribute 필드는 다음과 같은 경우 필수입니다 :values면 있어야합니다 .',
    'required_without'     => ':Attribute 필드는 다음과 같은 경우 필수입니다 :values 지금은 아니야.',
    'required_without_all' => ':Attribute 사용할 수없는 경우 필드가 필요합니다. :values 면 있다.',
    'same' => ':Attribute và :other 일치해야합니다.',
    'size' => 
    [
        'numeric' => ':Attribute 있어야한다 :size.',
        'file'    => ':Attribute 있어야한다 :size kilobytes.',
        'string'  => ':Attribute 있어야한다 :size 문자.',
        'array'   => ':Attribute 있어야한다 :size 품목.',
    ],
    'starts_with' => ':Attribute 다음 중 하나로 시작해야합니다.: :values.',
    'string'      => ':Attribute 문자열이어야합니다..',
    'timezone'    => ':Attribute 유효한 지역이어야합니다..',
    'unique'      => ':Attribute 등록되었다.',
    'uploaded'    => ':Attribute 업로드 할 수 없습니다..',
    'url'         => ':Attribute 잘못된 형식.',
    'uuid'        => ':Attribute 유효한 UUID 여야합니다..',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => 
    [
        'attribute-name' => 
        [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
