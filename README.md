Dotfiles
========

This is my collection of [configuration files](http://dotfiles.github.io/) for Ubuntu Linux

Usage
-----

Pull the repository, and then create the symbolic links [using GNU
stow](https://www.gnu.org/software/stow/)

```bash
$ git clone git@github.com:relizont/dotfiles.git ~/dotfiles && cd
~/dotfiles
$ stow fish vim tmux # plus whatever else you'd like
```

The `fish` dotfiles depend on [the fish shell](http://fishshell.com),
so install that first:

```bash
$ sudo apt-get install fish
$ chsh -s `which fish`
```

The Vim dotfiles depend on [Janus: Vim Distribution](https://github.com/carlhuda/janus)

```bash
$ curl -L https://bit.ly/janus-bootstrap | bash
```

The `bash` dotfiles depend on [Bash-it](https://github.com/Bash-it/bash-it),
so install that first:

```bash
$ git clone --depth=1 https://github.com/Bash-it/bash-it.git ~/.bash_it
$ sh ~/.bash_it/install.sh
```

The `zsh` dotfiles depend on [Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh),
so install that first:

```bash
$ sudo apt-get install zsh
$ sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
$ git clone https://github.com/zsh-users/zsh-completions ~/.oh-my-zsh/custom/plugins/zsh-completions
```

License
-------

[MIT](http://opensource.org/licenses/MIT).
