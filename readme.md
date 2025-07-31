
# 关于 BE双驱框架（Beyond Exception）


## 普通PHP和Swoole双驱动
BE框架底层内置两套驱动，支持普通PHP和Swoole两种方式部署，跟据不同部署环境启用不同的驱动，兼顾便捷与性能。

## C10K高并发，高可用
基于Swoole的常驻内存，连接池，对象池，异步协程等特性，轻松实现C10K（每秒1万并发）

## 开发友好，无门槛
开发人员无需关注Swoole特性，仅需按照传统PHP方式开发，入门级PHP程序员可轻松驾驭高可用系统

## 低代码，快速迭代
BE框架对常用功能进行了封装(如：存储、计划任务、CURD、表单、报表等)，大量使用注解自动实现(如：菜单、权限、路由、配置项等)，实际开发中仅需要编写少量调用代码

## 持续升级，快速响应
BE框架正处于高速成长期，项目将长期快速迭代，持续升级改进，并对出现的问题快速响应，同时我们在使用中收集到的优秀创意将沉淀在框架设计中。

## 开源，无需费用
BE框架遵循完全没有限制的MIT开源协议，您可以放心使用，修改，并部署到商业化项目中。


## 如何使用

### 拉取项目代码

> composer create-project be/new


### 普通模式部署

普通模式下入口文件为 www/index.php

php标准环境， apache+php 或 nginx+php，根目录指向 www


### Swoole模式部署

Swoole模式下入口文件为 server.php

#### Swoole模式方案一：php标准环境下，安装swoole

```shell
 php server.php start
 ```


#### Docker部署

```shell
docker run -d --name=be  -p 80:80 phpbe/be:latest
```

挂载 phpbe 目录，源码，安装的应用，缓存，日志等都将何存在该目录中

```shell
docker run -d --name=be -v /path/to/phpbe:/phpbe -p 80:80 phpbe/be:latest
```


#### Docker compose 部署

```shell
git clone https://git.junyouji.com/phpbe/be
cd be/docker-compose
docker compose up -d
```


## 访问系统，自动跳转到安装界面

> http://localhost


## 网站
    
https://www.phpbe.com
