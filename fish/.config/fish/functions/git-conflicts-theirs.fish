function git-conflicts-theirs
	grep -lr '<<<<<<<' . | xargs git checkout --theirs $argv;
end
