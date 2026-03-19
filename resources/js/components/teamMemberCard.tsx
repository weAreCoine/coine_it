import type { TeamMember } from '@/types/dto/sections';

type TeamMemberCardProps = {
    member: TeamMember;
};

export default function TeamMemberCard({ member }: TeamMemberCardProps) {
    return (
        <div className="group flex flex-col justify-start gap-8 bg-white p-4 sm:flex-row">
            <div className="flex max-w-40 shrink-0 flex-col justify-center bg-black">
                <div className="h-full overflow-hidden">
                    <img
                        src={member.image}
                        alt={member.name}
                        className="h-full w-auto scale-100 object-cover brightness-125 grayscale duration-300 group-hover:scale-110 group-hover:brightness-100 group-hover:grayscale-0"
                    />
                </div>
            </div>
            <div className="grow">
                <div className="flex flex-col items-baseline gap-2 xs:flex-row">
                    <p className="text-xl font-semibold">{member.name}</p>
                    <p className="kicker text-xs">{member.role}</p>
                </div>
                <p className="text-sm">{member.bio}</p>
                <div className="mt-3 flex flex-wrap gap-2">
                    {member.tags.map((tag) => (
                        <span key={tag} className="border border-mercury-200 px-3 py-1 text-xs">
                            {tag}
                        </span>
                    ))}
                </div>
            </div>
        </div>
    );
}
