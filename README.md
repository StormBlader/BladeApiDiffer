BladePhp 
名如其实

刀锋所致，所向披靡

一个轻量级的php框架

BladeApiDiffer 曾几何时，你优化接口却要依靠自己一点点去对比结果值？

别着急，可能是世界上最好用的接口对比工具诞生了，

只需要你的环境上部署一套master代码，和一套test分支的代码， 轻轻的输入参数和请求uri

剩下的事情我们帮你做！！！

搭建步骤
1. git clone
2. composer install
3. mysql导入apidiffer.sql
4. 配置nginx的rewrite try_files $uri $uri/ @rewrite; location @rewrite { rewrite ^(.*)$ /index.php?_url=$1;  }
3. 开跑
