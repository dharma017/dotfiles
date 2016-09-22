function gtrans
	gawk -f (curl -Ls git.io/translate | psub) -shell $argv;
end
