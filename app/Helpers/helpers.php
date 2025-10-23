<?php

if (!function_exists('convert_price')) {
    function convert_price(mixed $price = '', $flag = false)
    {
        if ($price === null) return 0;
        return ($flag === false) ? str_replace('.', '', $price) : number_format($price, 0, ',', '.');
    }
}

if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '')
    {
        $temp = [];
        if (is_array($system)) {
            foreach ($system as $key => $val) {
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if (is_object($system)) {
            foreach ($system as $key => $val) {
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }
        return $temp;
    }
}



if (!function_exists('renderSystemInput')) {
    function renderSystemInput(string $name = '', $systems = null)
    {
        return '
            <input type="text" 
            name="config[' . $name . ']"  
            value="' . old($name, ($systems[$name]) ?? '') . '"  
            class="form-control" 
            placeholder=""                    
            autocomplete="off" 
            id="">
        ';
    }
}


if (!function_exists('renderSystemImages')) {
    function renderSystemImages(string $name = '', $systems = null)
    {
        return '
            <input type="text" 
            name="config[' . $name . ']"  
            value="' . old($name, ($systems[$name]) ?? '') . '"  
            class="form-control upload-image" 
            placeholder=""                    
            autocomplete="off" 
            id="">
        ';
    }
}


if (!function_exists('renderSystemTextarea')) {
    function renderSystemTextarea(string $name = '', $systems = null)
    {
        $value = old($name, $systems[$name] ?? '');
        return '<textarea name="config[' . $name . ']" class="form-control system-textarea">' . e($value) . '</textarea>';
    }
}

if (!function_exists('renderSystemEditor')) {
    function renderSystemEditor(string $name = '', $systems = null)
    {
        $value = old($name, $systems[$name] ?? '');
        return '<textarea name="config[' . $name . ']" id="' . $name . '" class="form-control system-textarea ck-editor">' . e($value) . '</textarea>';
    }
}


if (!function_exists('renderSystemLink')) {
    function renderSystemLink(array $item = [])
    {
        return (isset($item['link']))
            ? '<a target="' . $item['link']['target'] . '" href="' . $item['link']['href'] . '">' . $item['link']['text'] . '</a>'
            : '';
    }
}


if (!function_exists('renderSystemTitle')) {
    function renderSystemTitle(array $item = [])
    {
        return (isset($item['title']))
            ? '<span class="system-link text-danger">' . $item['title'] . '</span>'
            : '';
    }
}

if (!function_exists('renderSystemSelect')) {
    function renderSystemSelect(array $item, string $name = '', $systems = null)
    {
        $html = '<select class="form-control" name="config[' . $name . ']">';

        if (isset($item['option']) && is_array($item['option'])) {
            foreach ($item['option'] as $key => $val) {
                $html .= '<option ' . (isset($systems[$name]) && ($key == $systems[$name]) ? 'selected' : '') . ' value="' . $key . '">' . $val . '</option>';
            }
        }

        $html .= '</select>';

        return $html;
    }
}

if (!function_exists('recursive')) {
    function recursive($data, $parentId = 0)
    {
        $temp = [];
        if (!is_null($data) && count($data)) {
            foreach ($data as $key => $val) {
                if ($val->parent_id == $parentId) {
                    $temp[] = [
                        'item' => $val,
                        'children' => recursive($data, $val->id)
                    ];
                }
            }
        }
        return $temp;
    }
}

if (!function_exists('recursive_menu')) {
    function recursive_menu($data)
    {
        $html = '';
        if (count($data)) {
            foreach ($data as $key => $val) {
                $itemId = $val['item']->id;
                $itemName = $val['item']->languages->first()->pivot->name;
                $itemUrl = route('menu.children', ['id' => $itemId]);

                $html .= "<li class='dd-item' data-id='$itemId'>";
                $html .= "<div class='dd-handle'>";
                $html .= "<span class='label label-info'><i class='fa fa-arrows'></i></span> $itemName";
                $html .= "</div>";
                $html .= "<a class='create-children-menu' href='$itemUrl'> Quản lý menu con </a>";

                if (count($val['children'])) {
                    $html .= "<ol class='dd-list'>";
                    $html .= recursive_menu($val['children']);
                    $html .= "</ol>";
                }

                $html .= "</li>";
            }
        }
        return $html;
    }
}

if (!function_exists('image')) {
    function image($image)
    {


        if (is_null($image)) return 'backend/img/not-found.jpg';

        $image = str_replace('//public/', '/', $image);

        return $image;
    }
}

if (!function_exists('buildMenu')) {
    function buildMenu($menus = null, $parent_id = 0, $prefix = '')
    {
        $output = [];
        $count = 1;

        if (count($menus)) {
            foreach ($menus as $key => $val) {
                if ($val->parent_id == $parent_id) {
                    $val->position = $prefix . $count;
                    $output[] = $val;
                    $output = array_merge($output, buildMenu($menus, $val->id, $val->position . '.'));
                    $count++;
                }
            }
        }
        return $output;
    }
}

if (!function_exists('loadClass')) {
    function loadClass(string $model = '', $folder = 'Repositories', $interface = 'Repository')
    {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        return $serviceInstance;
    }
}

if (!function_exists('convertArrayByKey')) {
    function convertArrayByKey($object = null, $fields = [])
    {
        $temp = [];
        foreach ($object as $key => $val) {
            foreach ($fields as $field) {
                if (is_array($object)) {
                    $temp[$field][] = $val[$field];
                } else {
                    $extract = explode('.', $field);
                    if (count($extract) == 2) {
                        if ($extract[1] == 'languages') {
                            $temp[$extract[0]][] = $val->{$extract[1]}->first()->pivot->{$extract[0]};
                        } else {
                            $temp[$extract[0]][] = $val->pivot->{$extract[0]};
                        }
                    } else {
                        $temp[$field][] = $val->{$field};
                    }
                }
            }
        }
        return $temp;
    }
}


function convertDateTime(?string $date = null, string $format = 'd/m/Y H:i', string $inputDateFormat = 'Y-m-d H:i:s')
{
    if (empty($date)) return '';
    try {
        return \Carbon\Carbon::createFromFormat($inputDateFormat, $date)->format($format);
    } catch (\Exception $e) {
        return '';
    }
}


if (!function_exists('renderDiscountInformation')) {
    function renderDiscountInformation($promotion = [])
    {
        if ($promotion->method === 'product_and_quantity') {
            $discountValue = $promotion->discountInformation['info']['discountValue'];
            $discountType = ($promotion->discountInformation['info']['discountType'] == 'percent') ? '%' : 'đ';
            return '<span class="label label-success">' . $discountValue . $discountType . ' </span>';
        }
        return  '<div><a href="' . route('promotion.edit', $promotion->id) . '">Xem chi tiết</a></div>';
    }
}
if (!function_exists('sortString')) {
    function sortString($string = '')
    {
        $extract = explode(',', $string);
        $extract = array_map('trim', $extract);
        sort($extract, SORT_NUMERIC);
        $newArray = implode(',', $extract);
        return $newArray;
    }
}
