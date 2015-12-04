/*商品详细通用函数*/
var priceControl = {
    spec: {
        "decimals":2,
        "dec_point":".",
        "thousands_sep":"",
        "sign":"\uffe5"
    },
    format: function(num, force) {
        var part;
        var sign = this.spec.sign || '';
        if (!(num || num === 0) || isNaN(+num)) return num;
        var num = parseFloat(num);
        if (this.spec.cur_rate) {
            num = num * this.spec.cur_rate;
        }
        num = Math.round(num * Math.pow(10, this.spec.decimals)) / Math.pow(10, this.spec.decimals) + '';
        var p = num.indexOf('.');
        if (p < 0) {
            p = num.length;
            part = '';
        } else {
            part = num.substr(p + 1);
        }
        while (part.length < this.spec.decimals) {
            part += '0';
        }
        var curr = [];
        while (p > 0) {
            if (p > 2) {
                p -= 3;
                curr.unshift(num.substr(p, 3));
            } else {
                curr.unshift(num.substr(0, p));
                break;
            }
        }
        if (!part) {
            this.spec.dec_point = '';
        }
        if (force) {
            sign = '<span class="price-currency">' + sign + '</span>';
        }
        return sign + curr.join(this.spec.thousands_sep) + this.spec.dec_point + part;
    },
    number: function(format) {
        if (!format) return null;
        if (isNaN(+format)) {
            if (format instanceof jQuery || (format.nodeName && format.nodeType === 1)) format = $(format).val() || $(format).text();
            if (format.indexOf(this.spec.sign) == 0) format = format.split(this.spec.sign)[1];
        }
        return +format;
    },
    calc: function(calc, n1, n2, noformat) {
        if (!(n1 || n1 === 0)) return null;
        if (!n2) {
            n1 = this.number(n1);
        }
        else {
            calc = !calc || calc == 'add' ? 1 : - 1;
            var t1 = 1,
            t2 = 1;
            if (n1 instanceof Array && n1.length) {
                t1 = n1[1];
                n1 = n1[0];
            }
            if (n2 instanceof Array && n2.length) {
                t2 = n2[1];
                n2 = n2[0];
            }
            var decimals = Math.pow(10, this.spec.decimals * this.spec.decimals);
            n1 = Math.abs(t1 * decimals * this.number(n1) + calc * t2 * decimals * this.number(n2)) / decimals;
        }
        if (!noformat) n1 = this.format(n1);
        return n1;
    },
    add: function(n1, n2, flag) {
        return this.calc('add', n1, n2, flag);
    },
    diff: function(n1, n2, flag) {
        return this.calc('diff', n1, n2, flag);
    }
};

//更新购物车数量
function updateCartNumber(content) {
    content = $(content || '.up-cat-number');
    var number = $.cookie('CARTNUMBER');
    $(content).text(number);
}

$(function(){
    //click thumbnail to show the big image
    $('body').on('click', '.show-pics a', function(e) {
        console.log('aa');
        e.preventDefault();
        var imgUrl = $(this).attr('href');
        if(!imgUrl){
            return false;
        }else{
            $.dialog($('<div><div class="show-pic-dialog"><div class="show-img"><img src="'+ imgUrl +'"></div></div></div>'), {
            width: 500,
            modal: true,
            title: '查看大图'
          });
        };
    });
})

