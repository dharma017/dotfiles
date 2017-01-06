Dotfiles
========

This is my collection of [configuration files](http://dotfiles.github.io/) for Ubuntu Linux

Usage
-----

Install GNU Stow: symlink farm manager

```bash
sudo apt-get install stow
```

The `fish` dotfiles depend on [the fish shell](http://fishshell.com),
so install that first:

```bash
$ sudo apt-get install fish
$ chsh -s `which fish`
```

Basic fish configuration

```bash
mkdir -p ~/.config/fish
vim ~/.config/fish/config.fish
set -g -x PATH /usr/local/bin $PATH
fish_config
fish_update_completions
echo "set -g -x fish_greeting ''" >> ~/.config/fish/config.fish
```

Install plugin manager for fish
```bash
curl -Lo ~/.config/fish/functions/fisher.fish --create-dirs git.io/fisher
fisher z fzf edc/bass omf/thefuck
__fzf_install
```

Install app which corrects your previous console command
```
sudo apt update
sudo apt install python3-dev python3-pip
sudo -H pip3 install thefuck
```

The Vim dotfiles depend on [Janus: Vim Distribution](https://github.com/carlhuda/janus)

```bash
sudo apt-get install ruby-dev rake exuberant-ctags ack-grep
curl -L https://bit.ly/janus-bootstrap | bash
```

Self-contained, pretty and versatile [.tmux.conf](https://github.com/gpakosz/.tmux) configuration file
```bash
cd
git clone https://github.com/gpakosz/.tmux.git
ln -s -f .tmux/.tmux.conf
cp .tmux/.tmux.conf.local .
```

Then proceed to customize your `~/.tmux.conf.local` copy.


The `bash` dotfiles depend on [Bash-it](https://github.com/Bash-it/bash-it),
so install if necessary

```bash
git clone --depth=1 https://github.com/Bash-it/bash-it.git ~/.bash_it
sh ~/.bash_it/install.sh
```

The `zsh` dotfiles depend on [Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh),
so install if necessary:

```bash
sudo apt-get install zsh
sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
git clone https://github.com/zsh-users/zsh-completions ~/.oh-my-zsh/custom/plugins/zsh-completions
```

Pull the repository, and then create the symbolic links [using GNU
stow](https://www.gnu.org/software/stow/)

```bash
git clone https://github.com/relizont/dotfiles.git ~/dotfiles && cd
~/dotfiles
stow fish vim # plus whatever else you'd like
mkdir -p ~/.vim/backup
mkdir -p ~/.vim/swap
```

Sublime dotfiles

```bash
rm -rf ~/.config/sublime-text-3/
stow sublime
```

License
-------

[MIT](http://opensource.org/licenses/MIT).
