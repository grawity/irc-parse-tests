IrcMessage = Struct.new(:tags, :prefix, :argv) do
	def unparse
		vec = []
		vec << "@" + tags	if tags
		vec << ":" + prefix	if prefix
		vec += argv		if argv
		return join(vec)
	end
end

def parse(str)
	vec = str.chomp.split(" ")
	i = 0
	tags = nil
	prefix = nil
	argv = []
	if vec[i].start_with? "@"
		tags = vec[i][1..-1]
		i += 1
	end
	if vec[i].start_with? ":"
		prefix = vec[i][1..-1]
		i += 1
	end
	while i < vec.length and !vec[i].start_with? ":"
		argv << vec[i]
		i += 1
	end
	if vec[i]
		trailing = vec[i..-1].join(" ")
		argv << trailing[1..-1]
	end
	return IrcMessage.new(tags, prefix, argv)
end

def join(args)
	i = 0
	vec = []
	if args[i].start_with? "@"
		if args[i] =~ /\s/
			raise "Argument #{i} contains whitespace"
		end
		vec << args[i]
		i += 1
	end
	if args[i].start_with? ":"
		if args[i] =~ /\s/
			raise "Argument #{i} contains whitespace"
		end
		vec << args[i]
		i += 1
	end
	n = args.length - 1
	while i < n
		if args[i].start_with? ":"
			raise "Argument #{i} starts with ':'"
		elsif args[i] =~ /\s/
			raise "Argument #{i} contains whitespace"
		end
		vec << args[i]
		i += 1
	end
	if args[i].start_with? ":" or args[i] =~ /\s/
		vec << ":" + args[i]
	else
		vec << args[i]
	end
	return vec.join(" ")
end