# Upgrade Guide

## 0.x to 1.x

Previously we had model within that package you had to extend but now you don't, you just need to use our trait like you would with `HasUlid` & `HasUuid` in Laravel

```diff
<?php

namespace App\Models;

- use Malico\LaravelNanoid\Eloquent\Model;
+ use Illuminate\Database\Eloquent\Model;
+ use Malico\LaravelNanoid\HasNanoids;

class Payment extends Model {
+    use HasNanoids;
}
```

Also, we support 2 methods, `nanoidPrefix` and `nanoidLength`, like

```php
public function nanoidLength(): int|array {
    // return [10,15]
    return 2;
}

public function nanoidPrefix(): string {
    return 'pay_'
}
```
