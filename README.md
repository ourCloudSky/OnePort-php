# OnePort
[![license](https://img.shields.io/apm/l/vim-mode.svg)](https://github.com/ourCloudSky/OnePort/blob/master/LICENSE)
A Super Fast And Good Proxy Written in PHP.

[![OnePort](https://github.com/ourCloudSky/OnePort/raw/master/docs/logo.png)](https://github.com/ourCloudSky/OnePort)
[![CloudSky](https://avatars0.githubusercontent.com/u/32470726?v=4&s=200)](https://github.com/ourCloudSky)

## 文档 Document
[https://docs.xapps.top/docs/show/1](https://docs.xapps.top/docs/show/1)

## 特性 Feature
- Fast, Responsive, Cross-platform | 快速，响应式，跨平台
- Written with PHP | 使用 PHP 编写
- Allow to set Muiti-User Password | 可以为多个用户分别设置密码
- Allow to encrypt your data | 可以对数据加密传输
- Do more than PortMap, Lighter than PortMap | 比端口映射做得更多，比端口映射更轻快
- Free, Open-Source, Easy-to-use | 免费，开源，便于使用

## 使用场景 Where-to-use
- My ISP only allows me to use one port | 我的网络提供商只允许我使用一个外网端口
- I want to use intranet mapping services to map my port, but I can only map one port. | 我想映射内网端口，但我只能映射一个
- I want to manage my server in only one port, and I want to encrypt all my activities, because there are lots of important data in my server | 我想只用一个端口访问我的服务器，并且我对安全的要求非常高，因为上面有许多我的重要数据
- My company have only one outside network port port, but there are ERP, OA in different ports even different servers | 我的公司只有一个外网端口，但 ERP, OA 等却在不同端口，甚至在不同的服务器上
- I want to access my internal network, but I cannot use VPN or intranet mapping | 我希望访问我的内网，但我不会用虚拟专用网络和内网映射
- I want to turn Socket to WebSocket without editing the program | 我想把 Socket 不经修改程序转为 WebSocket
- I only have 80 port and I want to run OnePort and HttpServer together | 我只有 80 端口却想将 OnePort 与 Http 服务一起运行
> ** OnePort 允许你以自由地以非商业化模式使用，商业（除集成至 CloudSky）使用请联系邮箱 xtl@xtlsoft.top ,以便我们提供更完善的服务。**

## 灵感来源
作者一台服务器，一开始 ISP 只开80端口，为了一起使用 Web, RDP, MySQL, NoSQL, SSH, WebSocket 等服务，费劲脑筋上网查找，发现找不到。虽然后来联系 ISP 关闭了 WAF，全端口映射，但是可能有的小伙伴还有这种问题，故开发了 OnePort。
