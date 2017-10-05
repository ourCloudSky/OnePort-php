# OnePort
A Super Fast And Good Proxy Written in PHP.

[![OnePort](https://github.com/ourCloudSky/OnePort/raw/master/docs/logo.png)](https://github.com/ourCloudSky/OnePort)
[![CloudSky](https://avatars0.githubusercontent.com/u/32470726?v=4&s=200)](https://github.com/ourCloudSky)

## 特性 Feature
- Fast, Responsive, Cross-platform | 快速，响应式，跨平台
- Written with PHP | 使用PHP编写
- Allow to set Muiti-User Password | 可以为多个用户分别设置密码
- Allow to encrypt your data | 可以对数据加密传输
- Do more than PortMap, Lighter than PortMap | 比端口映射做得更多，比端口映射更轻薄
- Free, Open-Source, Easy-to-use | 免费，开源，便于使用

## 使用场景 Where-to-use
- My ISP only allows me to use one port | 我的网络提供商只允许我使用一个外网端口
- I want to use FRP/Ngrok/Oray to map my port, but I only have the money to map one port. | 我想映射内网端口，但我只有钱映射一个
- I want to manage my server in only one port, and I want to encrypt all my activities, because there are lots of important things in my server | 我想只用一个端口访问我的服务器，并且我对安全的要求非常高，因为上面有许多我的重要数据
- My company have only one "OutNetwork(??)" port, but there are ERP, OA in different ports even different servers | 我的公司只有一个外网端口，但ERP,OA等却在不同端口，甚至在不同的服务器上
- I want to access my "Inside-Network(??)", but I cannot use VPN or Ngrok | 我希望访问我的内网，但我不会用虚拟专用网络和内网映射
- I want to turn Socket to WebSocket without editing the program | 我想把Socket不经修改程序转为WebSocket
- I only have 80 port and I want to run OnePort and HttpServer together | 我只有80端口却想将OnePort与Http服务一起运行
> **本端口映射限于私有云计算服务等使用，商业/公共使用请联系邮箱xtl@xtlsoft.top，集成到CloudSky的除外。 **

## 灵感来源
作者一台服务器，一开始ISP只开80端口，为了一起使用Web, RDP, MySQL, NoSQL, SSH, WebSocket等服务，费劲脑筋上网查找，发现找不到。虽然后来联系ISP关闭了WAF，全端口映射，但是可能有的小伙伴可能还有疑问。
