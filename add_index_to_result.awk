#!/bin/gawk -f

{
    str = $0;
    gsub(/[^0-9]*/, " ", str);
    split(str, arr, " ");
    asort(arr);
    print arr[1], arr[2], arr[3], arr[4], $0;
}
