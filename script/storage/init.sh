#!/bin/bash

source $(cd `dirname $0`; pwd)/ttserver.sh

#默认将public/images/下的图片都存储到Tokyo Tyrant 中,也可以根据需求自定义目录存储
if [ x$1 = x ]
then
    src=$root/images
else
    src=$1
fi

i=1
find $src  -type f | grep -v LC_MESSAGES | while read file;
do
    #父进程被子进程干掉，直接exit
    ps -p $$ >/dev/null
    if [ $? = 1 ]
    then
        exit;
    fi

    #创建
    create "$file" $$ &
    echo 'save' $file

    #第一次测试保存一条，之后进行并发
    if [ $i -eq 1 ]
        echo '保存中。。。'
        wait
    then
      #并发100
      i=`expr $i + 1`;
      num=`expr $i % 100`
      if [ $num -eq 0 ]
      then
        echo '保存中。。。'
        wait
      fi
    fi
done

echo 'storage初始化数据成功';
